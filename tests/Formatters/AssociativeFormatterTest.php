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

it('maps correctly if delimiter is used in the list', function (): void {

    $selected = fzf(
        options: [
            1 => 'Apple: Fresh',
            2 => 'Orange: Not so fresh',
            3 => 'Grapefruit',
        ],
        arguments: [
            'fake-first' => true,
        ]
    );

    expect($selected)->toBe('1');
});

it('respects set delimiter', function (): void {

    $selected = fzf(
        options: [
            1 => 'Apple',
            2 => 'Orange',
            3 => 'Grapefruit',
        ],
        arguments: [
            'delimiter' => '%',
            'fake-first' => true,
        ]
    );

    expect($selected)->toBe('1');
})->todo('do an actual assertion');
