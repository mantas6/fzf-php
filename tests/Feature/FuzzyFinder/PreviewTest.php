<?php

declare(strict_types=1);

use Mantas6\FzfPhp\FuzzyFinder;
use Tests\FakeProcess;

use function Mantas6\FzfPhp\fzf;

beforeEach(fn () => FuzzyFinder::usingProcessClass(FakeProcess::class));

it('retrieves preview information', function (): void {
    FakeProcess::fakeRunning(fn (): false =>
        // get path of socket client
        // open socket process
        // dd(FakeProcess::$lastCommand);
        false);

    fzf(
        ['Apple', 'Orange', 'Grapefruit'],
        preview: fn (string $item) => strtoupper($item),
    );
});
