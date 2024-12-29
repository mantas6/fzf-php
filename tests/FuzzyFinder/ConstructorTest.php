<?php

use FzfPhp\FuzzyFinder;

use function FzfPhp\fzf;

beforeAll(fn () => FuzzyFinder::defaultCommand('./bin/fzf-stub'));

it('works in constructor mode', function () {
    $selection = fzf(['A', 'B', 'C']);

    expect($selection)->not->toBeEmpty();
});
