<?php

declare(strict_types=1);

use function Mantas6\FzfPhp\cell;
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

it('works with cell helper', function (): void {
    $selection = fzf(
        options: [
            ['name' => 'Apples', 'weight' => 1000],
            ['name' => 'Oranges', 'weight' => 2000],
            ['name' => 'Grapefruits', 'weight' => 3000],
        ],

        arguments: ['filter' => 'Oranges'],

        present: fn (array $item): array => [
            $item['name'],

            cell($item['weight'], fg: $item['weight'] > 2000 ? 'red' : 'green'),
        ],
    );

    expect($selection)->toBe(['name' => 'Oranges', 'weight' => 2000]);
})->note('assert the rendering');
