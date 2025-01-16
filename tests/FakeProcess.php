<?php

namespace Tests;

final class FakeProcess
{
    public static array $lastCommand;
    public static string $lastInput;

    public function __construct(
        private array $command,
        private string $input,
        private int $timeout,
    ) {
        static::$lastCommand = $command;
        static::$lastInput = $input;
    }

    public static function getLastCommandString(): string
    {
        return implode(' ', static::$lastCommand);
    }

    public function start(array $env): void
    {
        //
    }

    public function stop(): ?int
    {
        return 0;
    }

    public function isRunning()
    {
        return false;
    }

    public function getExitCode(): ?int
    {
        return 0;
    }

    public function getErrorOutput(): string
    {
        return '';
    }

    public function getOutput(): string
    {
        $options = explode(PHP_EOL, $this->input);

        return $options[0];
    }
}
