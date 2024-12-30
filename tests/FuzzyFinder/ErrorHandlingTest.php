<?php

use FzfPhp\Exceptions\ProcessException;
use FzfPhp\FuzzyFinder;

use function FzfPhp\fzf;

beforeAll(fn () => FuzzyFinder::usingCommand('./bin/fzf-fake'));
afterAll(fn () => FuzzyFinder::usingDefaultCommand());

it('does not throw if exit code 130 is returned', function (): void {
    $selected = fzf(
        options: ['Apple', 'Orange', 'Grapefruit'],
        arguments: ['exit-130' => true],
    );

    expect($selected)->toBeEmpty();
});

it('does not throw if exit code 1 is returned', function (): void {
    $selected = fzf(
        options: ['Apple', 'Orange', 'Grapefruit'],
        arguments: ['exit-1' => true],
    );

    expect($selected)->toBeEmpty();
});

it('throws if exit code 2 is returned', function (): void {
    expect(
        fn (): string => fzf(
            options: ['Apple', 'Orange', 'Grapefruit'],
            arguments: ['exit-2' => true],
        )
    )->toThrow(ProcessException::class);
});