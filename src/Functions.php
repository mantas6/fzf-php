<?php

declare(strict_types=1);

namespace Mantas6\FzfPhp;

if (!function_exists('FzfPhp\fzf')) {
    /**
     * @param  array <int, string>  $options
     * @param  array <string, mixed>  $arguments
     */
    function fzf(array $options = [], array $arguments = []): string
    {
        return (new FuzzyFinder)
            ->arguments($arguments)
            ->ask($options);
    }
}
