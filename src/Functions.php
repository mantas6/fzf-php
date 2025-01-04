<?php

declare(strict_types=1);

namespace Mantas6\FzfPhp;

use Closure;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableCellStyle;

if (!function_exists('FzfPhp\fzf')) {
    function fzf($options = null, array $arguments = [], ?Closure $present = null): mixed
    {
        $finder = (new FuzzyFinder)
            ->arguments($arguments);

        if ($present !== null) {
            $finder->present($present);
        }

        if ($options !== null) {
            return $finder->ask($options);
        }

        return $finder;
    }
}

if (!function_exists('FzfPhp\cell')) {
    function cell(
        $value,
        string $align = TableCellStyle::DEFAULT_ALIGN,
        string $fg = 'default',
        string $bg = 'default',
        int $colspan = 1,
    ): mixed {
        return new TableCell((string) $value, [
            'colspan' => $colspan,
            'style' => new TableCellStyle([
                'align' => $align,
                'fg' => $fg,
                'bg' => $bg,
            ]),
        ]);
    }
}
