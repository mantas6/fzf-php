<?php

declare(strict_types=1);

namespace Mantas6\FzfPhp;

use Closure;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableCellStyle;

if (!function_exists('FzfPhp\fzf')) {
    function fzf(
        $options = null,
        array $arguments = [],
        ?array $headers = null,
        ?Closure $present = null,
        ?Closure $preview = null,
    ): mixed {
        $finder = (new FuzzyFinder)
            ->arguments($arguments);

        if ($headers !== null) {
            $finder->headers($headers);
        }

        if ($present instanceof Closure) {
            $finder->present($present);
        }

        if ($preview instanceof Closure) {
            $finder->preview($preview);
        }

        return $finder->ask($options);
    }
}

if (!function_exists('FzfPhp\cell')) {
    function cell(
        $value,
        string $align = TableCellStyle::DEFAULT_ALIGN,
        string $fg = 'default',
        string $bg = 'default',
        int $colspan = 1,
    ): TableCell {
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
