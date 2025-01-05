<?php

declare(strict_types=1);

use Mantas6\FzfPhp\FuzzyFinder;

use function Mantas6\FzfPhp\fzf;

beforeEach(fn () => FuzzyFinder::usingCommand(['./bin/fzf-fake']));

it('passes keyed arguments', function (): void {
    $selection = fzf(
        options: ['Apple', 'Orange', 'Grapefruit'],
        arguments: ['height' => '40%'],
    );

    expect($selection)->toContain(' --height 40%');
})->skip();

it('passes non keyed arguments', function (): void {
    $selection = fzf(
        options: ['Apple', 'Orange', 'Grapefruit'],
        arguments: ['wrap' => true],
    );

    expect($selection)->toContain(' --wrap');
})->skip();

it('does not pass arguments with false values', function (): void {
    $selection = fzf(
        options: ['Apple', 'Orange', 'Grapefruit'],
        arguments: ['wrap' => false],
    );

    expect($selection)->not->toContain(' --wrap');
})->skip();

it('does not pass single letter arguments with false values', function (): void {
    $selection = fzf(
        options: ['Apple', 'Orange', 'Grapefruit'],
        arguments: ['i' => false],
    );

    expect($selection)->not->toContain(' -i');
})->skip();

it('passes single letter arguments', function (): void {
    $selection = fzf(
        options: ['Apple', 'Orange', 'Grapefruit'],
        arguments: ['i' => true],
    );

    expect($selection)
        ->toContain(' -i');
})->skip();

it('passes single letter keyed arguments', function (): void {
    $selection = fzf(
        options: ['Apple', 'Orange', 'Grapefruit'],
        arguments: ['d' => ':'],
    );

    expect($selection)
        ->toContain(' -d :');
})->skip();
