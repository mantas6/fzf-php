<?php

declare(strict_types=1);

use Mantas6\FzfPhp\FuzzyFinder;
use Tests\FakeProcess;

use function Mantas6\FzfPhp\fzf;

beforeEach(fn () => FuzzyFinder::usingProcessClass(FakeProcess::class));

it('adds default arguments when set', function (): void {
    FuzzyFinder::usingDefaultArguments(['pointer' => '->']);

    fzf(['Apple', 'Orange', 'Grapefruit']);

    expect(FakeProcess::getLastCommandString())->toContain(' --pointer ->');
});

it('adds to override default arguments', function (): void {
    FuzzyFinder::usingDefaultArguments(['pointer' => '->']);

    fzf(
        options: ['Apple', 'Orange', 'Grapefruit'],
        arguments: ['pointer' => '>>'],
    );

    expect(FakeProcess::getLastCommandString())->toContain(' --pointer >>');
});
