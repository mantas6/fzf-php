<?php

use function Mantas6\FzfPhp\fzf;

class Presented
{
    public function __construct(public string $name) {}
}

it('works with objects when presentor is provided', function (): void {
    $apple = new Presented('Apple');

    $selection = fzf(
        options: [
            $apple,
            new Presented('Orange'),
            new Presented('Grapefruit'),
        ],

        arguments: ['filter' => 'Apple'],

        present: fn (Presented $item): array => [$item->name],
    );

    expect($selection)->toBe($apple);
});
