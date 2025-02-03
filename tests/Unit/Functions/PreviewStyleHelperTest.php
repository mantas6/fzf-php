<?php

declare(strict_types=1);

use Mantas6\FzfPhp\Support\PreviewStyleHelper;

use function Mantas6\FzfPhp\style;

it('render the output', function (): void {
    $style = style();
    expect($style)->toBeInstanceOf(PreviewStyleHelper::class);

    $style->table(
        headers: ['Name', 'Count'],
        rows: [
            ['Apples', 12],
            ['Oranges', 24],
        ],
    );

    $style->block('Fresh fruits');

    $text = $style->render();

    expect(trim($text))->toMatchSnapshot();
});
