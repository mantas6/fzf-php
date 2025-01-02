<?php

use Mantas6\FzfPhp\FuzzyFinder;

use function Mantas6\FzfPhp\fzf;

it('returns an FuzzyFinder instance when options are not passed', function (): void {
    $finder = fzf();

    expect($finder)->toBeInstanceOf(FuzzyFinder::class);

    $finder = fzf(arguments: ['height' => '40%']);

    expect($finder)->toBeInstanceOf(FuzzyFinder::class);
});
