<?php

use Mantas6\FzfPhp\FuzzyFinder;

use function Mantas6\FzfPhp\fzf;

beforeAll(function (): void {
    if (file_exists('./vendor/bin/fzf')) {
        unlink('./vendor/bin/fzf');
    }

    FuzzyFinder::usingDefaultCommand();
});

it('installs fzf binary when called', function (): void {
    fzf(['Apple'], ['filter' => 'Apple']);

    expect('./vendor/bin/fzf')->toBeFile();
});
