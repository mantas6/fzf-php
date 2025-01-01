<?php

declare(strict_types=1);

namespace Mantas6\FzfPhp;

use Mantas6\FzfPhp\Concerns\Formatter;

if (!function_exists('FzfPhp\fzf')) {
    /**
     * @param  array <int, string>  $options
     * @param  array <string, mixed>  $arguments
     */
    function fzf(?array $options = null, array $arguments = [], ?Formatter $format = null): mixed
    {
        $finder = (new FuzzyFinder)
            ->format($format)
            ->arguments($arguments);

        if ($options !== null) {
            return $finder->ask($options);
        }

        return $finder;
    }
}
