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

    protected static string $defaultBinary = './vendor/bin/fzf';

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
        static::$command = [static::$defaultBinary];
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

    /**
     * @param  array <int, string>  $options
     * @return null|string|array <int, string>
     */
    public function ask(array $options = []): string|array|null
    {
        $input = new InputStream;

        $process = new Process(
            command: [...static::resolveCommand(), ...$this->buildArguments()],
            input: $input,
            timeout: 0,
        );

        $process->start(env: [
            'FZF_DEFAULT_COMMAND' => null,
            'FZF_DEFAULT_OPTS' => null,
            'FZF_DEFAULT_OPTS_FILE' => null,
        ]);

        $input->write(implode(PHP_EOL, $options));
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

        $selected = explode(
            PHP_EOL,
            $process->getOutput()
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

        return [$basePath.'/'.static::$defaultBinary];
    }
}
