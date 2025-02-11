<?php

declare(strict_types=1);

use Mantas6\FzfPhp\FuzzyFinder;
use Mantas6\FzfPhp\Support\PreviewStyleHelper;
use Mantas6\FzfPhp\ValueObjects\FinderEnv;
use Symfony\Component\Process\Process;
use Tests\FakeProcess;
use Tests\FakeProcessHelper;

use function Mantas6\FzfPhp\fzf;
use function Mantas6\FzfPhp\style;

beforeEach(fn () => FuzzyFinder::usingProcessClass(FakeProcess::class));

it('passes preview parameter to fzf', function (): void {
    FakeProcessHelper::preview();

    fzf(
        ['Apple', 'Orange', 'Grapefruit'],
        preview: fn (string $item) => strtoupper($item),
    );

    $value = FakeProcess::getCommandAfter('--preview');

    expect($value)->toEndWith('preview {}')
        ->toContain('bin/fzf-php-socket ')
        ->toContain(' unix://');
});

it('retrieves preview information', function (): void {
    FakeProcessHelper::preview(function (Process $process): void {
        expect($process->getExitCode())
            ->toBe(0)
            ->and($process->getOutput())
            ->toBe('APPLE');
    });

    fzf(
        ['Apple', 'Orange', 'Grapefruit'],
        preview: fn (string $item) => strtoupper($item),
    );

    expect(static::getCount())->toBe(2);
});

it('retrieves preview information using style helper', function (): void {
    FakeProcessHelper::preview(function (Process $process): void {
        expect($process->getExitCode())
            ->toBe(0)
            ->and(trim($process->getOutput()))
            ->toMatchSnapshot();
    });

    fzf(
        ['Apple', 'Orange', 'Grapefruit'],
        preview: fn (string $item): PreviewStyleHelper => style()
            ->table([
                'Original' => $item,
                'Uppercase' => strtoupper($item),
            ])
            ->block($item)
    );

    expect(static::getCount())->toBe(2);
});

it('passes through env variables', function (): void {
    FakeProcessHelper::preview();

    fzf(
        ['Apple', 'Orange', 'Grapefruit'],
        preview: function ($_, FinderEnv $env): void {
            expect($env->query)->toBe('test');
        },
    );

    expect(static::getCount())->toBe(1);
});
