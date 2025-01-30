<?php

declare(strict_types=1);

namespace Tests;

use Closure;
use Symfony\Component\Process\Process;

class FakeProcessHelper
{
    public static function preview(Closure $assertions): void
    {
        FakeProcess::fakeRunning(function () use ($assertions): bool {
            if (FakeProcess::getContext() === null) {
                $options = explode(PHP_EOL, FakeProcess::$lastInput);

                $previewCmd = FakeProcess::getCommandAfter('--preview');
                $previewCmd = str_replace('{}', $options[0], $previewCmd);

                $process = new Process(
                    explode(' ', $previewCmd),
                );

                FakeProcess::setContext($process);

                $process->start();
            }

            /** @var Process */
            $process = FakeProcess::getContext();

            if ($process->isRunning()) {
                return true;
            }

            $assertions($process);

            return false;
        });
    }
}
