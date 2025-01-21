<?php

declare(strict_types=1);

use Mantas6\FzfPhp\FuzzyFinder;
use Tests\FakeProcess;

use function Mantas6\FzfPhp\cell;
use function Mantas6\FzfPhp\fzf;

beforeEach(fn () => FuzzyFinder::usingProcessClass(FakeProcess::class));

it('renders basic options correctly', function (): void {
    fzf(['Apple', 'Orange', 'Grapefruit']);

    expect(FakeProcess::$lastInput)->toMatchSnapshot();
});

it('renders presented options correctly', function (): void {
    fzf(
        ['Apple', 'Orange', 'Grapefruit'],
        present: fn (string $item): array => [
            $item,
            strtoupper($item),
            cell($item, fg: 'red'),
        ],
    );

    expect(FakeProcess::$lastInput)->toMatchSnapshot();
});
