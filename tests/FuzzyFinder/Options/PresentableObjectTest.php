<?php

declare(strict_types=1);

use Mantas6\FzfPhp\Concerns\PresentsForFinder;

use function Mantas6\FzfPhp\fzf;

class Presentable implements PresentsForFinder
{
    public function __construct(public string $name) {}

    public function presentForFinder(): array
    {
        return [$this->name];
    }

    public function toArray(): string
    {
        return 'badly coded method';
    }
}

it('works with objects that implement interface', function (): void {
    $apple = new Presentable('Apple');

    $selection = fzf(
        options: [
            $apple,
            new Presentable('Orange'),
            new Presentable('Grapefruit'),
        ],
        arguments: ['filter' => 'Apple'],
    );

    expect($selection)->toBe($apple);
});
