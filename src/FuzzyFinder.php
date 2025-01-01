<?php

namespace Mantas6\FzfPhp;

use Composer\Autoload\ClassLoader;
use Mantas6\FzfPhp\Concerns\Formatter;
use Mantas6\FzfPhp\Exceptions\ProcessException;
use Mantas6\FzfPhp\Formatters\AssociativeFormatter;
use Mantas6\FzfPhp\Formatters\NullFormatter;
use Symfony\Component\Process\InputStream;
use Symfony\Component\Process\Process;

class FuzzyFinder
{
    /** @var array <string, string|bool> */
    protected array $arguments = [];

    /** @var array <string, mixed> */
    protected static array $defaultArguments = [];

    protected static string $defaultBinary = './vendor/bin/fzf';

    protected ?Formatter $formatter = null;

    /** @var array <string> */
    protected static ?array $command = null;

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

    public function format(?Formatter $formatter = null): self
    {
        $this->formatter = $formatter;

        return $this;
    }

    /**
     * @param  array <string, mixed>  $args
     */
    public function arguments(array $args): self
    {
        $this->arguments = $args;

        return $this;
    }

    /**
     * @param  array <mixed>  $options
     * @return null|mixed|string|array <mixed>
     */
    public function ask(array $options = []): mixed
    {
        static::prepareBinary();

        if (!$this->formatter instanceof Formatter) {
            $this->formatter = match (true) {
                !array_is_list($options) => new AssociativeFormatter,
                default => new NullFormatter,
            };
        }

        $input = new InputStream;

        $arguments = $this->getAllArguments();

        $process = new Process(
            command: [...static::resolveCommand(), ...$this->prepareArgumentsForCommand($arguments)],
            input: $input,
            timeout: 0,
        );

        $process->start(env: [
            'FZF_DEFAULT_COMMAND' => null,
            'FZF_DEFAULT_OPTS' => null,
            'FZF_DEFAULT_OPTS_FILE' => null,
        ]);

        $input->write(implode(PHP_EOL, $this->formatter->before($options, $arguments)));
        $input->close();
        $process->wait();

        $exitCode = $process->getExitCode();
        $error = $process->getErrorOutput();

        if ($exitCode !== 0) {
            if (in_array($exitCode, [1, 130])) {
                return $this->isMultiMode() ? [] : null;
            }

            throw new ProcessException($error !== '' && $error !== '0' ? $error : "Process exited with code $exitCode");
        }

        $selected = $this->formatter->after(
            selected: explode(
                PHP_EOL,
                $process->getOutput()
            ),

            arguments: $arguments,
        );

        if ($this->isMultiMode()) {
            return $selected;
        }

        return $selected[0];
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
            ...$this->formatter->arguments(
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
