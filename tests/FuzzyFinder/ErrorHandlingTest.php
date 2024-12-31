<?php

use Mantas6\FzfPhp\Exceptions\ProcessException;
use Mantas6\FzfPhp\FuzzyFinder;

use function Mantas6\FzfPhp\fzf;

beforeAll(fn () => FuzzyFinder::usingCommand(['./bin/fzf-fake']));
afterAll(fn () => FuzzyFinder::usingDefaultCommand());

it('does not throw if exit code 130 is returned', function (): void {
    $selected = fzf(
        options: ['Apple', 'Orange', 'Grapefruit'],
        arguments: ['fake-exit-130' => true],
    );

    expect($selected)->toBeEmpty();
});

it('does not throw if exit code 1 is returned', function (): void {
    $selected = fzf(
        options: ['Apple', 'Orange', 'Grapefruit'],
        arguments: ['fake-exit-1' => true],
    );

    expect($selected)->toBeEmpty();
});

it('throws if exit code 2 is returned', function (): void {
    expect(
        fn (): string => fzf(
            options: ['Apple', 'Orange', 'Grapefruit'],
            arguments: ['fake-exit-2' => true],
        )
    )->toThrow(ProcessException::class);
});
