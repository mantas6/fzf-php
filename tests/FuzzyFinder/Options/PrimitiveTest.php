<?php

declare(strict_types=1);

use function Mantas6\FzfPhp\fzf;

it('works with strings', function (): void {
    $selection = fzf(
        ['Apple', 'Orange', 'Grapefruit'],
        arguments: ['filter' => 'Apple'],
    );

    expect($selection)->toBe('Apple');
});

it('works with arrays', function (): void {
    $selection = fzf(
        options: [
            ['Apples', '1kg'],
            ['Oranges', '2kg'],
            ['Grapefruits', '3kg'],
        ],
        arguments: ['filter' => 'Apple'],
    );

    expect($selection)->toBe(['Apples', '1kg']);
});

it('works with non list arrays', function (): void {
    $selection = fzf(
        options: [
            20 => ['Apples', '1kg'],
            35 => ['Oranges', '2kg'],
            36 => ['Grapefruits', '3kg'],
        ],
        arguments: ['filter' => 'Oranges'],
    );

    expect($selection)->toBe(['Oranges', '2kg']);
});
