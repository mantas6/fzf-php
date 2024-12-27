<?php

declare(strict_types=1);

namespace FzfPhp;

if (!function_exists('FzfPhp\fzf')) {
    function fzf(array|callable $options): string
    {
        return (new Fzf)
            ->options($options)
            ->run();
    }
}
