<?php

declare(strict_types=1);

use Mantas6\FzfPhp\FuzzyFinder;

use function Mantas6\FzfPhp\fzf;

afterAll(fn () => FuzzyFinder::usingDefaultCommand());

beforeAll(function (): void {
    FuzzyFinder::usingCommand(['./bin/fzf-fake']);

    // putenv('FZF_DEFAULT_COMMAND=testing');
    // putenv('FZF_DEFAULT_OPTS=testing');
    // putenv('FZF_DEFAULT_OPTS_FILE=testing');
});

it('doesnt retain system fzf env variables', function (): void {
    $result = fzf([], ['fake-dump-env' => true, 'multi' => true]);

    expect($result)->not->toContain('FZF_DEFAULT_COMMAND')
        ->not->toContain('FZF_DEFAULT_OPTS')
        ->not->toContain('FZF_DEFAULT_OPTS_FILE');
})->todo('need to find a way to fake the env variables');
