<?php

use FzfPhp\Downloader;
use FzfPhp\FuzzyFinder;

it('installs fzf binary', function (): void {
    $binPath = './vendor/bin/fzf';
    if (! file_exists($binPath)) {
        Downloader::installLatestRelease();
    }

    expect(file_exists($binPath))->toBe(true);
    expect(filesize($binPath))->not->toBe(0);

    $versionInfo = (new FuzzyFinder)
        ->arguments(['version' => true])
        ->ask();

    expect($versionInfo)->not->toBeEmpty();
});
