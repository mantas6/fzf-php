<?php

declare(strict_types=1);

use function Mantas6\FzfPhp\fzf;

it('returns string in single mode', function (): void {
    $selection = fzf(
        ['Apple', 'Orange', 'Grapefruit'],
        arguments: ['filter' => 'Apple'],
    );

    expect($selection)->not->toBeEmpty()
        ->not->toBeArray();
});

it('returns array in multi mode with short flag', function (): void {
    $selection = fzf(
        options: ['Apple', 'Orange', 'Grapefruit'],
        arguments: ['filter' => 'Apple', 'multi' => true],
    );

    expect($selection)->toBeArray();
});

it('returns array in multi mode with long flag', function (): void {
    $selection = fzf(
        options: ['Apple', 'Orange', 'Grapefruit'],
        arguments: ['filter' => 'Apple', 'multi' => true],
    );

    expect($selection)->toBeArray();
});

it('returns empty array if user cancels', function (): void {
    $selected = fzf(
        options: ['Apple', 'Orange', 'Grapefruit'],
        arguments: [
            'filter' => 'Non existant fruit',
            'multi' => true,
        ],
    );

    expect($selected)->not->toBeNull()
        ->toBe([]);
});
