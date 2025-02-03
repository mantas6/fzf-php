<?php

declare(strict_types=1);

use Mantas6\FzfPhp\ValueObjects\FinderEnv;

it('queries env array', function (): void {
    $vars = [
        'FZF_POS' => '7',
        'FZF_TOTAL_COUNT' => '20',
    ];

    $env = new FinderEnv($vars);

    expect($env->pos)->toBe('7');
    expect($env->totalCount)->toBe('20');
});
