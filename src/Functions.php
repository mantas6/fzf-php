<?php

declare(strict_types=1);

namespace Mantas6\FzfPhp;

if (!function_exists('Mantas6\FzfPhp\fzf')) {
    function fzf(array|callable $options): string
    {
        return (new Fzf)
            ->options($options)
            ->run();
    }
}
