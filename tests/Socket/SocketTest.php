<?php

use Mantas6\FzfPhp\Socket;

it('creates and unlinks socket file', function () {
    $socket = new Socket;
    $path = $socket->start();
    expect($path)->toBeFile();

    $socket->stop();
    expect($path)->not->toBeFile();
});
