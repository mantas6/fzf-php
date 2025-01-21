<?php

declare(strict_types=1);

use Mantas6\FzfPhp\FuzzyFinder;

pest()->beforeEach(function (): void {
    FuzzyFinder::usingDefaultCommand();
    FuzzyFinder::usingDefaultArguments([]);
    FuzzyFinder::usingDefaultProcessClass();
});
