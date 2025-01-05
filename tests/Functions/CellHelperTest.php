<?php

declare(strict_types=1);

use Symfony\Component\Console\Helper\TableCell;

use function Mantas6\FzfPhp\cell;

it('fills table cell object', function (): void {
    $cell = cell(
        value: 'some text',
        align: 'right',
        fg: 'red',
        bg: 'green',
        colspan: 2,
    );

    expect($cell)->toBeInstanceOf(TableCell::class);
    expect($cell->__toString())->toBe('some text');
    expect($cell->getColspan())->toBe(2);
    expect($cell->getStyle()->getOptions())
        ->toBe([
            'fg' => 'red',
            'bg' => 'green',
            'options' => null,
            'align' => 'right',
            'cellFormat' => null,
        ]);
});
