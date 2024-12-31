<?php

use Mantas6\FzfPhp\FuzzyFinder;

use function Mantas6\FzfPhp\fzf;

beforeAll(fn () => FuzzyFinder::usingCommand(['./bin/fzf-fake']));
afterAll(fn () => FuzzyFinder::usingDefaultCommand());

it('returns string in single mode', function (): void {
    $selection = fzf(['Apple', 'Orange', 'Grapefruit']);

    expect($selection)->not->toBeEmpty()
        ->not->toBeArray();
});

it('returns array in multi mode with short flag', function (): void {
    $selection = fzf(
        options: ['Apple', 'Orange', 'Grapefruit'],
        arguments: ['m' => true],
    );

    expect($selection)->toBeArray();
});

it('returns array in multi mode with long flag', function (): void {
    $selection = fzf(
        options: ['Apple', 'Orange', 'Grapefruit'],
        arguments: ['multi' => true],
    );

    expect($selection)->toBeArray();
});

it('returns empty array if user cancels', function (): void {
    $selected = fzf(
        options: ['Apple', 'Orange', 'Grapefruit'],
        arguments: [
            'multi' => true,
            'fake-exit-1' => true,
        ],
    );

    expect($selected)->not->toBeNull()
        ->toBe([]);
});
