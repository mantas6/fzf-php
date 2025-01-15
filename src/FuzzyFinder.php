<?php

declare(strict_types=1);

namespace Mantas6\FzfPhp;

use Closure;
use Mantas6\FzfPhp\Concerns\PresentsForFinder;
use Mantas6\FzfPhp\Exceptions\ProcessException;
use Mantas6\FzfPhp\Support\CompactTable;
use Mantas6\FzfPhp\Support\Helpers;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;
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

    protected ?Closure $presenter = null;

    public ?Closure $preview = null;

    /**
     * @param  array <string>  $cmd
     */
    public static function usingCommand(array $cmd): void
    {
        static::$command = $cmd;
    }

    public static function usingDefaultCommand(): void
    {
        static::$command = null;
    }

    /**
     * @param  array <string, mixed>  $args
     */
    public static function usingDefaultArguments(array $args): void
    {
        static::$defaultArguments = $args;
    }

    /**
     * @param  array <string, mixed>  $args
     */
    public function arguments(array $args): self
    {
        $this->arguments = $args;

        return $this;
    }

    public function present(Closure $presenter): self
    {
        $this->presenter = $presenter;

        return $this;
    }

    /**
     * @param  array <mixed>  $options
     * @return null|mixed|string|array <mixed>
     */
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

        $process = new Process(
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
            $socket->listen(fn (string $input) => $this->respondToSocket($input, $options));
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
        $input = explode(PHP_EOL, $input);
        $action = array_shift($input);
        $selection = implode(PHP_EOL, $input);

        $mapped = $this->mapFinderOutput([$selection], $options);

        $buf = new BufferedOutput(decorated: true);

        $io = new SymfonyStyle(new StringInput(''), $buf);

        ($this->preview)($mapped[0], $io);

        return $buf->fetch();
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

        $processed = [];

        foreach ($options as $key => $value) {
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
            $this->presenter instanceof Closure => ($this->presenter)($value),
            // Strings
            is_string($value) => [$value],
            // Interface
            $value instanceof PresentsForFinder => $value->presentForFinder(),
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

        (new CompactTable($output))->display($options);

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
        $basePath = Helpers::basePath();

        return [
            'ansi' => true,

            ...$arguments,

            'd' => false,
            'delimiter' => static::$delimiter,
            'with-nth' => '2..',
            'preview' => "$basePath/bin/fzf-socket unix://$socketPath preview {}",
        ];
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
            Downloader::installLatestRelease();
        }
    }
}
