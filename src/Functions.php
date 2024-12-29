<?php

declare(strict_types=1);

namespace FzfPhp;

if (! function_exists('FzfPhp\fzf')) {
    function fzf(array|callable $options = null): FuzzyFinder|string
    {
        if ($options === null) {
            return new FuzzyFinder;
        }

        return (new FuzzyFinder)
            ->options($options)
            ->run();
    }
}
