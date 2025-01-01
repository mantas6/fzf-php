<?php

use Mantas6\FzfPhp\FuzzyFinder;

use function Mantas6\FzfPhp\fzf;

beforeAll(fn () => FuzzyFinder::usingCommand(['./bin/fzf-fake']));
afterAll(fn () => FuzzyFinder::usingDefaultCommand());

it('maps selection correctly', function (): void {
    $finder = fzf(arguments: ['fake-first' => true]);

    $selected = $finder->ask([
        1 => 'Apple',
        2 => 'Orange',
        3 => 'Grapefruit',
    ]);

    expect($selected)->toBe('1');

    $selected = $finder->ask([
        3 => 'Grapefruit',
        2 => 'Orange',
        1 => 'Apple',
    ]);

    expect($selected)->toBe('3');
});
