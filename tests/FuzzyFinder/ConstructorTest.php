<?php

use Mantas6\FzfPhp\FuzzyFinder;

use function Mantas6\FzfPhp\fzf;

beforeAll(fn () => FuzzyFinder::usingCommand('./bin/fzf-fake'));
afterAll(fn () => FuzzyFinder::usingDefaultCommand());

it('works in constructor mode', function (): void {
    $selection = fzf(['Apple', 'Orange', 'Grapefruit']);

    expect($selection)->toContain('Grapefruit');
});

it('passes keyed arguments', function (): void {
    $selection = fzf(
        options: ['Apple', 'Orange', 'Grapefruit'],
        arguments: ['height' => '40%'],
    );

    expect($selection)->toContain(' --height 40%');
});

it('passes non keyed arguments', function (): void {
    $selection = fzf(
        options: ['Apple', 'Orange', 'Grapefruit'],
        arguments: ['wrap' => true],
    );

    expect($selection)->toContain(' --wrap');
});

it('passes single letter arguments', function (): void {
    $selection = fzf(
        options: ['Apple', 'Orange', 'Grapefruit'],
        arguments: ['i' => true],
    );

    expect($selection)
        ->toContain(' -i');
});

it('passes single letter keyed arguments', function (): void {
    $selection = fzf(
        options: ['Apple', 'Orange', 'Grapefruit'],
        arguments: ['d' => ':'],
    );

    expect($selection)
        ->toContain(' -d :');
});
