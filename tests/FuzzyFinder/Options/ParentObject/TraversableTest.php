<?php

declare(strict_types=1);

use function Mantas6\FzfPhp\fzf;

it('works with traversable type', function (): void {
    $iterator = new ArrayIterator(['Apple', 'Orange', 'Grapefruit']);

    $selection = fzf(
        options: $iterator,
        arguments: ['filter' => 'Apple'],
    );

    expect($selection)->toBe('Apple');
});
