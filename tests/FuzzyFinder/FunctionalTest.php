<?php

use FzfPhp\FuzzyFinder;

use function FzfPhp\fzf;

beforeAll(fn () => FuzzyFinder::defaultCommand('./bin/fzf-stub'));

it('works in functional mode', function () {
    $selection = fzf()
        ->stubFirst()
        ->options(['A', 'B', 'C'])
        ->run();

    expect($selection)->toBe('A');
});
