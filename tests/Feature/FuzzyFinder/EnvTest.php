<?php

declare(strict_types=1);

use Mantas6\FzfPhp\FuzzyFinder;
use Tests\FakeProcess;

use function Mantas6\FzfPhp\fzf;

beforeEach(fn () => FuzzyFinder::usingProcessClass(FakeProcess::class));

it('doesnt retain system fzf env variables', function (): void {
    fzf(['Apples', 'Oranges']);

    expect(FakeProcess::$lastEnv)->not->toContain('FZF_DEFAULT_COMMAND')
        ->not->toContain('FZF_DEFAULT_OPTS')
        ->not->toContain('FZF_DEFAULT_OPTS_FILE');
});
