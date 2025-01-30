<?php

declare(strict_types=1);

use Mantas6\FzfPhp\Socket;

it('creates and unlinks socket file', function (): void {
    $socket = new Socket;
    $path = $socket->start();
    expect($path)->toBeFile();
    expect(dirname($path))->toBeDirectory();

    $socket->stop();
    expect($path)->not->toBeFile();
    expect(dirname($path))->not->toBeDirectory();
});
