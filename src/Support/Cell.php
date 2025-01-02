<?php

namespace Mantas6\FzfPhp\Support;

use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableCellStyle;

class Cell
{
    public static function create(
        string $value,
        string $align = TableCellStyle::DEFAULT_ALIGN,
        string $fg = 'default',
        string $bg = 'default',
        int $colspan = 1,
    ): TableCell {
        return new TableCell($value, [
            'colspan' => $colspan,
            'style' => new TableCellStyle([
                'align' => $align,
                'fg' => $fg,
                'bg' => $bg,
            ]),
        ]);
    }
}
