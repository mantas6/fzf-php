<?php

namespace Mantas6\FzfPhp;

use Composer\Autoload\ClassLoader;
use Mantas6\FzfPhp\Exceptions\ProcessException;
use Symfony\Component\Process\InputStream;
use Symfony\Component\Process\Process;

class FuzzyFinder
{
    /** @var array <string, mixed> */
    protected array $arguments = [];

    /** @var array <string, mixed> */
    protected static array $defaultArguments = [];

    protected static string $defaultCommand = './vendor/bin/fzf';

    protected static ?string $command = null;

    public static function usingCommand(string $cmd): void
    {
        static::$command = $cmd;
    }

    public static function usingDefaultCommand(): void
    {
        static::$command = static::$defaultCommand;
    }

    /**
     * @param array <string, mixed> $args
     */
    public static function usingDefaultArguments(array $args): void
    {
        static::$defaultArguments = $args;
    }

    /**
     * @param array <string, mixed> $args
     */
    public function arguments(array $args): self
    {
        $this->arguments = $args;

        return $this;
    }

    /**
     * @param array <int, string> $options
     * @return string|array <int, string>
     */
    public function ask(array $options = []): string|array
    {
        $input = new InputStream;

        $process = new Process(
            command: [static::resolveCommand(), ...$this->buildArguments()],
            input: $input,
            timeout: 0,
        );

        $process->start();

        $input->write(implode(PHP_EOL, $options));
        $input->close();
        $process->wait();

        $exitCode = $process->getExitCode();
        $error = $process->getErrorOutput();

        if ($exitCode !== 0 && !in_array($exitCode, [1, 130])) {
            throw new ProcessException($error !== '' && $error !== '0' ? $error : "Process exited with code $exitCode");
        }

        $selected = explode(
            PHP_EOL,
            $process->getOutput()
        );

        if (!empty($this->arguments['multi']) || !empty($this->arguments['m'])) {
            return $selected;
        }

        return $selected[0];
    }

    /**
     * @return array <int<0, max>, string>
     */
    protected function buildArguments(): array
    {
        $arguments = [];

        foreach ([...static::$defaultArguments, ...$this->arguments] as $key => $value) {
            if ($value !== false) {
                $arguments[] = strlen($key) > 1 ? "--$key" : "-$key";

                if (is_string($value)) {
                    $arguments[] = $value;
                }
            }
        }

        return $arguments;
    }

    protected static function resolveCommand(): string
    {
        if (static::$command !== null && static::$command !== '' && static::$command !== '0') {
            return static::$command;
        }

        $basePath = dirname(
            array_keys(ClassLoader::getRegisteredLoaders())[0]
        );

        return $basePath.'/'.static::$defaultCommand;
    }
}
