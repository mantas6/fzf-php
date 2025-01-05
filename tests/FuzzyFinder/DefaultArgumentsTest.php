<?php

declare(strict_types=1);

use Mantas6\FzfPhp\FuzzyFinder;

use function Mantas6\FzfPhp\fzf;

beforeEach(fn () => FuzzyFinder::usingCommand(['./bin/fzf-fake']));

it('adds default arguments when set', function (): void {
    FuzzyFinder::usingDefaultArguments(['pointer' => '->']);

    $selection = fzf(['Apple', 'Orange', 'Grapefruit']);

    expect($selection)->toContain(' --pointer ->');
})->skip();

it('adds to override default arguments', function (): void {
    FuzzyFinder::usingDefaultArguments(['pointer' => '->']);

    $selection = fzf(
        options: ['Apple', 'Orange', 'Grapefruit'],
        arguments: ['pointer' => '>>'],
    );

    expect($selection)->toContain(' --pointer >>');
})->skip();
