<?php

declare(strict_types=1);

use Mantas6\FzfPhp\FuzzyFinder;
use Tests\FakeProcess;

use function Mantas6\FzfPhp\fzf;

beforeEach(fn () => FuzzyFinder::usingProcessClass(FakeProcess::class));

it('passes keyed arguments', function (): void {
    fzf(
        options: ['Apple', 'Orange', 'Grapefruit'],
        arguments: ['height' => '40%'],
    );

    expect(FakeProcess::getLastCommandString())->toContain(' --height 40%');
});

it('passes non keyed arguments', function (): void {
    fzf(
        options: ['Apple', 'Orange', 'Grapefruit'],
        arguments: ['wrap' => true],
    );

    expect(FakeProcess::getLastCommandString())->toContain(' --wrap');
});

it('does not pass arguments with false values', function (): void {
    $selection = fzf(
        options: ['Apple', 'Orange', 'Grapefruit'],
        arguments: ['wrap' => false],
    );

    expect(FakeProcess::getLastCommandString())->not->toContain(' --wrap');
});

it('does not pass single letter arguments with false values', function (): void {
    $selection = fzf(
        options: ['Apple', 'Orange', 'Grapefruit'],
        arguments: ['i' => false],
    );

    expect(FakeProcess::getLastCommandString())->not->toContain(' -i');
});

it('passes single letter arguments', function (): void {
    $selection = fzf(
        options: ['Apple', 'Orange', 'Grapefruit'],
        arguments: ['i' => true],
    );

    expect(FakeProcess::getLastCommandString())
        ->toContain(' -i');
});

it('passes single letter keyed arguments', function (): void {
    $selection = fzf(
        options: ['Apple', 'Orange', 'Grapefruit'],
        arguments: ['i' => ':'],
    );

    expect(FakeProcess::getLastCommandString())
        ->toContain(' -i :');
});
