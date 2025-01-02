<?php

use function Mantas6\FzfPhp\fzf;

class CustomCollection
{
    public function toArray(): array
    {
        return [
            'Apple',
            'Orange',
            'Grapefruit',
        ];
    }
}

it('works with collection classes', function (): void {

    $selection = fzf(
        options: new CustomCollection,
        arguments: ['filter' => 'Apple'],
    );

    expect($selection)->toBe('Apple');
});
