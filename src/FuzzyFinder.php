<?php

namespace Mantas6\FzfPhp;

use Composer\Autoload\ClassLoader;
use Mantas6\FzfPhp\Concerns\PresentsForFinder;
use Mantas6\FzfPhp\Exceptions\ProcessException;
use Mantas6\FzfPhp\Support\CompactTable;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Process\Process;

class FuzzyFinder
{
    /** @var array <string, string|bool> */
    protected array $arguments = [];

    /** @var array <string, mixed> */
    protected static array $defaultArguments = [];

    protected static string $defaultBinary = './vendor/bin/fzf';

    protected static string $delimiter = ':';

    protected static ?array $command = null;

    protected $presenter;

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

    public function present(callable $presenter): self
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

        $arguments = $this->getAllArguments();

        $options = array_values((array) $options);

        $process = new Process(
            command: [...static::resolveCommand(), ...$this->prepareArgumentsForCommand($arguments)],
            input: implode(PHP_EOL, $this->prepareOptionsForCommand($options)),
            timeout: 0,
        );

        $process->start(env: [
            'FZF_DEFAULT_COMMAND' => null,
            'FZF_DEFAULT_OPTS' => null,
            'FZF_DEFAULT_OPTS_FILE' => null,
        ]);

        $process->wait();

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

    protected function prepareOptionsForCommand($options): array
    {
        $options = $this->prepareOptionsForTable($options);
        $options = $this->convertOptionsToTable($options);

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
            $this->presenter !== null => call_user_func($this->presenter, $value),
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

    protected function convertOptionsToTable(array $options): array
    {
        $output = new BufferedOutput;

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

    protected function getOptionArguments(array $arguments): array
    {
        return [
            ...$arguments,
            'd' => false,
            'delimiter' => static::$delimiter,
            'with-nth' => '2..',
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

    protected function getAllArguments(): array
    {
        return [
            ...static::$defaultArguments,
            ...$this->getOptionArguments(
                $this->arguments,
            ),
        ];
    }

    /**
     * @return array <string>
     */
    protected static function resolveCommand(): array
    {
        if (static::$command !== null && static::$command !== []) {
            return static::$command;
        }

        $basePath = dirname(
            array_keys(ClassLoader::getRegisteredLoaders())[0]
        );

        return [$basePath . '/' . static::$defaultBinary];
    }

    protected static function prepareBinary(): void
    {
        if (static::$command === null && !file_exists(static::resolveCommand()[0])) {
            Downloader::installLatestRelease();
        }
    }
}
