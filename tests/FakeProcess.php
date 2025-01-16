<?php

declare(strict_types=1);

namespace Tests;

final class FakeProcess
{
    public static array $lastCommand;
    public static string $lastInput;
    public static int $lastTimeout;

    public static array $lastEnv;

    public function __construct(
        array $command,
        private readonly string $input,
        int $timeout,
    ) {
        self::$lastCommand = $command;
        self::$lastInput = $input;

        self::$lastTimeout = $timeout;
    }

    public static function getLastCommandString(): string
    {
        return implode(' ', self::$lastCommand);
    }

    public function start(array $env): void
    {
        self::$lastEnv = $env;
    }

    public function stop(): ?int
    {
        return 0;
    }

    public function isRunning(): bool
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
