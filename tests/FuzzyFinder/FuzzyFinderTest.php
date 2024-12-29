<?php

use FzfPhp\FuzzyFinder;

beforeAll(fn () => FuzzyFinder::defaultCommand('./bin/fzf-stub'));

it('executes external process and returns its result', function (): void {
    $result = (new FuzzyFinder)
        ->stubFirst()
        ->options(['A', 'B'])
        ->run();

    expect($result)->toBe('A');
});
