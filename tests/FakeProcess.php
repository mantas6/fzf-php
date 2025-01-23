<?php

declare(strict_types=1);

namespace Tests;

use Closure;

final class FakeProcess
{
    public static array $lastCommand;
    public static string $lastInput;
    public static int $lastTimeout;

    public static array $lastEnv;

    private static ?Closure $runningCallback = null;

    private static $context;

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

    public static function fakeRunning(Closure $callback): void
    {
        self::$runningCallback = $callback;
    }

    public static function setContext($context): void
    {
        self::$context = $context;
    }

    public static function getContext()
    {
        return self::$context;
    }

    public static function reset(): void
    {
        self::$runningCallback = null;
        self::$context = null;
    }

    public static function getCommandAfter(string $argument): mixed
    {
        $index = array_search($argument, self::$lastCommand);

        return self::$lastCommand[$index + 1];
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
        if (self::$runningCallback instanceof Closure) {
            return (self::$runningCallback)();
        }

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
