<?php

declare(strict_types=1);

namespace Tests;

use Closure;
use Symfony\Component\Process\Process;

class FakeProcessHelper
{
    public static function preview(?Closure $assertions = null): void
    {
        FakeProcess::fakeRunning(function () use ($assertions): bool {
            if (FakeProcess::getContext() === null) {
                $options = FakeProcess::getInputList();

                $previewCmd = FakeProcess::getCommandAfter('--preview');
                $previewCmd = str_replace('{}', $options[0], $previewCmd);

                $process = new Process(
                    explode(' ', $previewCmd),
                    env: [
                        'FZF_QUERY' => 'test',
                    ],
                );

                FakeProcess::setContext($process);

                $process->start();
            }

            /** @var Process */
            $process = FakeProcess::getContext();

            if ($process->isRunning()) {
                return true;
            }

            if ($assertions instanceof Closure) {
                $assertions($process);
            }

            return false;
        });
    }
}
