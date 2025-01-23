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
