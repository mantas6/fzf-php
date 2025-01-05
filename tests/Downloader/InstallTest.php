<?php

declare(strict_types=1);

use Mantas6\FzfPhp\Downloader;

use function Mantas6\FzfPhp\fzf;

$binPath = './vendor/bin/fzf';

beforeEach(fn (): ?bool => file_exists($binPath) ? unlink($binPath) : null);

it('installs fzf binary', function () use ($binPath): void {
    Downloader::installLatestRelease();

    expect($binPath)->toBeFile();
    expect(filesize($binPath))->not->toBe(0);

    fzf(['Apple'], ['filter' => 'Apple']);
});

it('installs fzf binary will script from bin', function () use ($binPath): void {
    exec('./bin/fzf-php-install', $output, $exitCode);

    expect($exitCode)->toBe(0);

    expect($binPath)->toBeFile();
    expect(filesize($binPath))->not->toBe(0);

    fzf(['Apple'], ['filter' => 'Apple']);
});
