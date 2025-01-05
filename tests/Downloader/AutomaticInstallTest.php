<?php

declare(strict_types=1);

use function Mantas6\FzfPhp\fzf;

beforeEach(function (): void {
    if (file_exists('./vendor/bin/fzf')) {
        unlink('./vendor/bin/fzf');
    }
});

it('installs fzf binary when called', function (): void {
    fzf(['Apple'], ['filter' => 'Apple']);

    expect('./vendor/bin/fzf')->toBeFile();
});
