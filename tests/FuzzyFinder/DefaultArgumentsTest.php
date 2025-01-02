<?php

use Mantas6\FzfPhp\FuzzyFinder;

use function Mantas6\FzfPhp\fzf;

beforeAll(fn () => FuzzyFinder::usingCommand(['./bin/fzf-fake']));
afterAll(fn () => FuzzyFinder::usingDefaultCommand());

afterEach(fn () => FuzzyFinder::usingDefaultArguments([]));

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
