<?php

declare(strict_types=1);

namespace Mantas6\FzfPhp;

use Closure;
use Mantas6\FzfPhp\Concerns\PresentsForFinder;
use Mantas6\FzfPhp\Enums\Action;
use Mantas6\FzfPhp\Exceptions\ProcessException;
use Mantas6\FzfPhp\Support\Helpers;
use Mantas6\FzfPhp\Support\PreviewStyleHelper;
use Mantas6\FzfPhp\ValueObjects\FinderEnv;
use Mantas6\FzfPhp\ValueObjects\SocketRequest;
use Mantas6\FzfPhp\ValueObjects\State;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Process\Process;
use Throwable;
use Traversable;

class FuzzyFinder
{
    /** @var array <string, string|bool> */
    protected array $arguments = [];

    /** @var array <string, mixed> */
    protected static array $defaultArguments = [];

    protected static string $defaultBinary = './vendor/bin/fzf';

    protected static string $delimiter = ':';

    protected static ?array $command = null;

    protected static $processClass = Process::class;

    protected ?Closure $presenter = null;

    protected ?Closure $preview = null;

    protected ?array $headers = null;

    protected State $state;

    public static function usingCommand(array $cmd): void
    {
        static::$command = $cmd;
    }

    public static function usingDefaultCommand(): void
    {
        static::$command = null;
    }

    public static function usingDefaultArguments(array $args): void
    {
        static::$defaultArguments = $args;
    }

    public static function usingProcessClass($class): void
    {
        static::$processClass = $class;
    }

    public static function usingDefaultProcessClass(): void
    {
        static::$processClass = Process::class;
    }

    public function arguments(array $args): self
    {
        $this->arguments = $args;

        return $this;
    }

    public function present(Closure $callback): self
    {
        $this->presenter = $callback;

        return $this;
    }

    public function preview(Closure $callback): self
    {
        $this->preview = $callback;

        return $this;
    }

    public function headers(array $headers): self
    {
        $this->headers = $headers;

        return $this;
    }

    public function ask($options): mixed
    {
        static::prepareBinary();

        $this->state = State::prepareDefault();

        $this->state->setArguments([
            ...static::$defaultArguments,
            ...$this->getInternalArguments($options),
        ]);

        $process = new static::$processClass(
            command: [...static::resolveCommand(), ...$this->prepareArgumentsForCommand()],
            input: $this->prepareInputForCommand($options),
            timeout: 0,
        );

        $process->start(env: [
            'FZF_DEFAULT_COMMAND' => null,
            'FZF_DEFAULT_OPTS' => null,
            'FZF_DEFAULT_OPTS_FILE' => null,
        ]);

        while ($process->isRunning()) {
            $this->state->socket->listen(function (string $requestJson) use ($options, $process): string {
                try {
                    $request = SocketRequest::fromJson($requestJson);

                    return match ($request->action) {
                        Action::Preview => $this->respondToPreview($request, $options),
                        Action::Reload => $this->respondToReload($request, $options),
                    };
                } catch (Throwable $e) {
                    $process->stop();
                    throw $e;
                }
            });
        }

        $this->state->socket->stop();

        $exitCode = $process->getExitCode();
        $error = $process->getErrorOutput();

        if ($exitCode !== 0) {
            if (in_array($exitCode, [1, 130])) {
                return $this->isMultiMode() ? [] : null;
            }

            throw new ProcessException($error !== '' && $error !== '0' ? $error : "Process exited with code $exitCode");
        }

        $selected = $this->mapFinderOutput(
            selected: explode(PHP_EOL, $process->getOutput()),
            options: $this->state->getAvailableOptions(),
        );

        if ($this->isMultiMode()) {
            return $selected;
        }

        return $selected[0];
    }

    protected function respondToReload(SocketRequest $request, Closure $optionsCallback): string
    {
        $options = $this->normalizeOptionsType(
            $this->processInvokableOptions($optionsCallback, $request->env),
        );

        $this->state->setAvailableOptions($options);

        $options = $this->prepareOptionsForCommand($options);

        return implode(PHP_EOL, $options);
    }

    protected function respondToPreview(SocketRequest $request, array $options): string
    {
        $mapped = $this->mapFinderOutput([$request->selection], $options);

        $output = ($this->preview)($mapped[0], $request->env);

        return match (true) {
            is_string($output) => $output,
            $output instanceof PreviewStyleHelper => $output->render(),
            default => (string) $output,
        };
    }

    protected function processInvokableOptions($options, FinderEnv $env): mixed
    {
        return match ($options instanceof Closure) {
            true => $options($env),
            false => $options,
        };
    }

    protected function normalizeOptionsType($options): array
    {
        return array_values(
            match (true) {
                // Collections
                $options instanceof Traversable => iterator_to_array($options),
                // toArray()
                is_object($options) && method_exists($options, 'toArray') => $options->toArray(),
                // ...
                default => (array) $options,
            }
        );
    }

    protected function prepareOptionsForCommand($options): array
    {
        $options = $this->prepareOptionsForTable($options);
        $options = $this->convertOptionsToTable($options);
        $arguments = $this->state->getArguments();

        $headersLinesCount = ($arguments['header-lines'] ?? false) ?: 0;

        $processed = [];

        foreach ($options as $key => $value) {
            $key -= $headersLinesCount;

            $processed[] = $key . static::$delimiter . $value;
        }

        return $processed;
    }

    protected function prepareOptionsForTable($options): array
    {
        $processed = [];

        foreach ($options as $value) {
            $processed[] = $this->prepareRowForTable($value);
        }

        return $processed;
    }

    protected function prepareRowForTable($value): array
    {
        return match (true) {
            // Presenter
            $this->presenter instanceof Closure => Helpers::alwaysArray(($this->presenter)($value)),
            // Strings
            is_string($value) => [$value],
            // Interface
            $value instanceof PresentsForFinder => Helpers::alwaysArray($value->presentForFinder()),
            // toArray()
            is_object($value) && method_exists($value, 'toArray') => $value->toArray(),
            // ...
            default => (array) $value,
        };
    }

    protected function convertOptionsToTable(array $options): array
    {
        $arguments = $this->state->getArguments();

        $output = new BufferedOutput(
            decorated: !empty($arguments['ansi']),
        );

        $table = new Table($output);

        $table->setStyle('compact')
            ->setRows($options);

        if ($this->headers !== null) {
            $table->setHeaders($this->headers);
        }

        $table->render();

        return array_filter(
            explode(PHP_EOL, $output->fetch())
        );
    }

    protected function mapFinderOutput(array $selected, array $options): array
    {
        $values = [];

        foreach (array_filter($selected) as $value) {
            $key = substr(
                (string) $value,
                0,
                strpos((string) $value, static::$delimiter),
            );

            $values[] = $options[$key];
        }

        return $values;
    }

    protected function getInternalArguments($options): array
    {
        $args = [
            'ansi' => true,

            ...$this->arguments,

            'd' => false,
            'delimiter' => static::$delimiter,
            'with-nth' => '2..',
        ];

        if ($this->headers !== null) {
            $args['header-lines'] = '1';
        }

        $socketPath = $this->state->socket->getPath();

        if ($this->preview instanceof Closure) {
            $binPath = Socket::getBinPath();
            $args['preview'] = "$binPath unix://$socketPath preview {}";
        }

        if ($options instanceof Closure) {
            $binPath = Socket::getBinPath();

            $args['bind'] = implode(',', [
                "change:reload($binPath unix://$socketPath reload)+first",
                "start:reload($binPath unix://$socketPath reload)+first",
            ]);

            $args['disabled'] = true;
        }

        return $args;
    }

    protected function isMultiMode(): bool
    {
        return !empty($this->arguments['multi']) || !empty($this->arguments['m']);
    }

    protected function prepareInputForCommand($options): ?string
    {
        if ($options instanceof Closure) {
            return null;
        }

        $optionsOnStart = $this->normalizeOptionsType($options);

        $this->state->setAvailableOptions($optionsOnStart);

        $preparedOptionsForCmd = $this->prepareOptionsForCommand($optionsOnStart);

        return implode(PHP_EOL, $preparedOptionsForCmd);
    }

    protected function prepareArgumentsForCommand(): array
    {
        $commandArguments = [];

        foreach ($this->state->getArguments() as $key => $value) {
            if ($value !== false) {
                $commandArguments[] = strlen($key) > 1 ? "--$key" : "-$key";

                if (is_string($value)) {
                    $commandArguments[] = $value;
                }
            }
        }

        return $commandArguments;
    }

    protected static function resolveCommand(): array
    {
        if (static::$command !== null && static::$command !== []) {
            return static::$command;
        }

        $basePath = Helpers::basePath();

        return [$basePath . '/' . static::$defaultBinary];
    }

    protected static function prepareBinary(): void
    {
        if (static::$command === null && !file_exists(static::resolveCommand()[0])) {
            Installer::handle();
        }
    }
}
