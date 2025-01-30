<?php

declare(strict_types=1);

use Mantas6\FzfPhp\FuzzyFinder;
use Symfony\Component\Process\Process;
use Tests\FakeProcess;

use function Mantas6\FzfPhp\fzf;

beforeEach(fn () => FuzzyFinder::usingProcessClass(FakeProcess::class));

it('retrieves preview information', function (): void {
    FakeProcess::fakeRunning(function (): bool {
        if (FakeProcess::getContext() === null) {
            $previewCmd = FakeProcess::getCommandAfter('--preview');
            $options = explode(PHP_EOL, FakeProcess::$lastInput);

            expect($options)->toHaveCount(3);

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

        expect($process->getExitCode())
            ->toBe(0)
            ->and($process->getOutput())
            ->toBe('APPLE');

        return false;
    });

    fzf(
        ['Apple', 'Orange', 'Grapefruit'],
        preview: fn (string $item) => strtoupper($item),
    );

    expect(static::getCount())->toBe(3);
});
