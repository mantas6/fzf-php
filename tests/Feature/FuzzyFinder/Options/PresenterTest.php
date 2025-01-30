<?php

declare(strict_types=1);

use Mantas6\FzfPhp\FuzzyFinder;
use Tests\FakeProcess;

use function Mantas6\FzfPhp\cell;
use function Mantas6\FzfPhp\fzf;

beforeEach(fn () => FuzzyFinder::usingProcessClass(FakeProcess::class));

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

        present: fn (Presented $item): array => [$item->name],
    );

    expect(FakeProcess::$lastInput)->toMatchSnapshot();

    expect($selection)->toBe($apple);
});

it('works when non array value is returned fron presntor', function (): void {
    $selection = fzf(
        options: [
            'Apple', 'Orange', 'Grapefruit',
        ],

        present: fn (string $item): string => $item,
    );

    expect(FakeProcess::$lastInput)->toMatchSnapshot();

    expect($selection)->toBe('Apple');
});

it('works with cell helper', function (): void {
    FakeProcess::setSelection(1);

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

    expect(FakeProcess::$lastInput)->toMatchSnapshot();

    expect($selection)->toBe(['name' => 'Oranges', 'weight' => 2000]);
});
