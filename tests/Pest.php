<?php

declare(strict_types=1);

use Mantas6\FzfPhp\FuzzyFinder;
use Tests\FakeProcess;

pest()->beforeEach(function (): void {
    FuzzyFinder::usingDefaultCommand();
    FuzzyFinder::usingDefaultArguments([]);
    FuzzyFinder::usingDefaultProcessClass();
    FakeProcess::reset();
});

expect()->pipe('toMatchSnapshot', function (Closure $next) {
    if (is_string($this->value)) {
        $this->value = trim($this->value);
    }

    return $next();
});
