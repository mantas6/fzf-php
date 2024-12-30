<?php

namespace FzfPhp;

use Composer\Autoload\ClassLoader;
use Symfony\Component\Process\InputStream;
use Symfony\Component\Process\Process;

class FuzzyFinder
{
    /** @var array <string, mixed> */
    protected array $arguments = [];

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
     * @param  array <string, mixed>  $args
     */
    public function arguments(array $args): self
    {
        $this->arguments = $args;

        return $this;
    }

    /**
     * @param  array <int, string>  $options
     */
    public function ask(array $options = []): string
    {
        $input = new InputStream;

        $command = [];

        foreach ($this->arguments as $key => $value) {
            if ($value !== false) {
                $command[] = strlen($key) > 1 ? "--$key" : "-$key";

                if ($value !== true) {
                    $command[] = $value;
                }
            }
        }

        $process = new Process(
            command: [static::resolveCommand(), ...$command],
            input: $input,
            timeout: 0,
        );

        $process->start();

        $input->write(implode("\n", $options));
        $input->close();
        $process->wait();

        // echo ($process->getExitCode());

        $error = $process->getErrorOutput();

        if ($error !== '' && $error !== '0') {
            throw new \Exception($error);
        }

        return $process->getOutput();
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
