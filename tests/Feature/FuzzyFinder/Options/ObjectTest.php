<?php

declare(strict_types=1);

use function Mantas6\FzfPhp\fzf;

class Model
{
    public function __construct(public string $name) {}

    public function toArray(): array
    {
        return [$this->name];
    }
}

it('works with objects that implement toArray', function (): void {
    $apple = new Model('Apple');

    $selection = fzf(
        options: [
            $apple,
            new Model('Orange'),
            new Model('Grapefruit'),
        ],
        arguments: ['filter' => 'Apple'],
    );

    expect($selection)->toBe($apple);
});
