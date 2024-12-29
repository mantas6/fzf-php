<?php

declare(strict_types=1);

namespace FzfPhp;

if (! function_exists('FzfPhp\fzf')) {
    function fzf(array|callable|null $options): string
    {
        if ($options === null) {
            return new FuzzyFinder;
        }

        return (new FuzzyFinder)
            ->options($options)
            ->run();
    }
}
