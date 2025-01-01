<?php

use Mantas6\FzfPhp\Downloader;
use Mantas6\FzfPhp\FuzzyFinder;

$binPath = './vendor/bin/fzf';

beforeEach(fn () => file_exists($binPath) ? unlink($binPath) : null);

it('installs fzf binary', function () use ($binPath): void {
    Downloader::installLatestRelease();

    expect($binPath)->toBeFile();
    expect(filesize($binPath))->not->toBe(0);

    $versionInfo = (new FuzzyFinder)
        ->arguments(['version' => true])
        ->ask();

    expect($versionInfo)->not->toBeEmpty();
});

it('installs fzf binary will script from bin', function () use ($binPath): void {
    exec('./bin/fzf-php-install', $output, $exitCode);

    expect($exitCode)->toBe(0);

    expect($binPath)->toBeFile();
    expect(filesize($binPath))->not->toBe(0);

    $versionInfo = (new FuzzyFinder)
        ->arguments(['version' => true])
        ->ask();

    expect($versionInfo)->not->toBeEmpty();
});
