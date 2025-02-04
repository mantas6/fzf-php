<?php

declare(strict_types=1);

use Mantas6\FzfPhp\FuzzyFinder;
use Tests\FakeProcess;

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

it('renders the headers', function (): void {
    FuzzyFinder::usingProcessClass(FakeProcess::class);
    FakeProcess::setSelection(1);

    fzf(
        options: [
            'Apple',
            'Orange',
            'Grapefruit',
        ],
        headers: ['Name'],
    );

    expect(FakeProcess::$lastInput)->toMatchSnapshot();
});
