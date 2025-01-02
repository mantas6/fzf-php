<?php

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
