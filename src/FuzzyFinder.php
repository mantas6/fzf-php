<?php

declare(strict_types=1);

namespace Mantas6\FzfPhp;

use Closure;
use Mantas6\FzfPhp\Concerns\PresentsForFinder;
use Mantas6\FzfPhp\Exceptions\ProcessException;
use Mantas6\FzfPhp\Support\Helpers;
use Mantas6\FzfPhp\Support\PreviewStyleHelper;
use Mantas6\FzfPhp\ValueObjects\FinderEnv;
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

    public function ask($options = []): mixed
    {
        static::prepareBinary();

        $socket = new Socket;
        $socketPath = $socket->start();

        $arguments = [
            ...static::$defaultArguments,
            ...$this->getInternalArguments(
                $this->arguments,
                $socketPath,
            ),
        ];

        $options = $this->normalizeOptionsType($options);

        $process = new static::$processClass(
            command: [...static::resolveCommand(), ...$this->prepareArgumentsForCommand($arguments)],
            input: implode(PHP_EOL, $this->prepareOptionsForCommand($options, $arguments)),
            timeout: 0,
        );

        $process->start(env: [
            'FZF_DEFAULT_COMMAND' => null,
            'FZF_DEFAULT_OPTS' => null,
            'FZF_DEFAULT_OPTS_FILE' => null,
        ]);

        while ($process->isRunning()) {
            $socket->listen(function (string $input) use ($options, $process): string {
                try {
                    return $this->respondToSocket($input, $options);
                } catch (Throwable $e) {
                    $process->stop();
                    throw $e;
                }
            });
        }

        $socket->stop();

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
            options: $options,
        );

        if ($this->isMultiMode()) {
            return $selected;
        }

        return $selected[0];
    }

    protected function respondToSocket(string $input, array $options): string
    {
        $input = json_decode($input, true);

        $mapped = $this->mapFinderOutput([$input['selection']], $options);

        $env = new FinderEnv($input['env']);

        $output = ($this->preview)($mapped[0], $env);

        return match (true) {
            is_string($output) => $output,
            $output instanceof PreviewStyleHelper => $output->render(),
            default => (string) $output,
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

    protected function prepareOptionsForCommand($options, array $arguments): array
    {
        $options = $this->prepareOptionsForTable($options);
        $options = $this->convertOptionsToTable($options, $arguments);

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

    protected function convertOptionsToTable(array $options, array $arguments): array
    {
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

    protected function getInternalArguments(array $arguments, string $socketPath): array
    {
        $args = [
            'ansi' => true,

            ...$arguments,

            'd' => false,
            'delimiter' => static::$delimiter,
            'with-nth' => '2..',
        ];

        if ($this->headers !== null) {
            $args['header-lines'] = '1';
        }

        if ($this->preview instanceof Closure) {
            $basePath = Helpers::basePath();
            $args['preview'] = "$basePath/vendor/bin/fzf-php-socket unix://$socketPath preview {}";
        }

        return $args;
    }

    protected function isMultiMode(): bool
    {
        return !empty($this->arguments['multi']) || !empty($this->arguments['m']);
    }

    /**
     * @return array <int<0, max>, string>
     */
    protected function prepareArgumentsForCommand(array $arguments): array
    {
        $commandArguments = [];

        foreach ($arguments as $key => $value) {
            if ($value !== false) {
                $commandArguments[] = strlen($key) > 1 ? "--$key" : "-$key";

                if (is_string($value)) {
                    $commandArguments[] = $value;
                }
            }
        }

        return $commandArguments;
    }

    /**
     * @return array <string>
     */
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
