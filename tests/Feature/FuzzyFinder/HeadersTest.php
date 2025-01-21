<?php

declare(strict_types=1);

use function Mantas6\FzfPhp\fzf;

it('select correct index with headers', function (): void {
    $selected = fzf(
        options: [
            'Apple',
            'Orange',
            'Grapefruit',
        ],
        headers: ['Name'],
        arguments: ['filter' => 'Orange'],
    );

    expect($selected)->toBe('Orange');
});
