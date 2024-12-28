<?php

use FzfPhp\FuzzyFinder;

beforeAll(fn () => FuzzyFinder::setBinaryPath('./bin/fzf-stub'));

it('executes external process and returns its result', function (): void {
    $result = (new FuzzyFinder)
        ->options(['A', 'B'])
        ->run();

    expect($result)->toBe("1\n");
});
