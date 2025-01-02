<?php

declare(strict_types=1);

namespace Mantas6\FzfPhp;

if (!function_exists('FzfPhp\fzf')) {
    /**
     * @param  array <int, string>  $options
     * @param  array <string, mixed>  $arguments
     */
    function fzf(?array $options = null, array $arguments = []): mixed
    {
        $finder = (new FuzzyFinder)
            ->arguments($arguments);

        if ($options !== null) {
            return $finder->ask($options);
        }

        return $finder;
    }
}
