<?php

declare(strict_types=1);

namespace Mantas6\FzfPhp;

if (!function_exists('FzfPhp\fzf')) {
    /**
     * @param  array <int, string>  $options
     * @param  array <string, mixed>  $arguments
     * @return null|string|array <int, string>
     */
    function fzf(array $options = [], array $arguments = []): string|array|null
    {
        return (new FuzzyFinder)
            ->arguments($arguments)
            ->ask($options);
    }
}
