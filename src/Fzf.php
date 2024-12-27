<?php

namespace FzfPhp;

use Composer\Autoload\ClassLoader;
use Symfony\Component\Process\InputStream;
use Symfony\Component\Process\Process;

class Fzf
{
    protected $options = [];

    protected array $command = [];

    public function __construct()
    {
        //
    }

    public function command(array $command): self
    {
        $this->command = $command;

        return $this;
    }

    public function options(array|callable $options): self
    {
        $this->options = $options;

        return $this;
    }

    public function __call($name, $arguments): self
    {
        $this->command[$name] = $arguments[0] ?? true;

        return $this;
    }

    public function run(): string
    {
        $input = new InputStream;

        $command = [];

        foreach ($this->command as $key => $value) {
            if ($value !== false) {
                $command[] = "--$key";

                if ($value !== true) {
                    $command[] = $value;
                }
            }
        }

        $vendorPath = array_keys(ClassLoader::getRegisteredLoaders())[0];
        $progPath = $vendorPath.'/bin/fzf';

        if (! file_exists($progPath)) {
            throw new \Exception('Binary is not downloaded');
        }

        $process = new Process(
            command: [$progPath, ...$command],
            input: $input,
            timeout: 0,
        );

        $process->start();

        $options = is_callable($this->options)
            ? call_user_func($this->options)
            : $this->options;

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
}
