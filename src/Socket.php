<?php

declare(strict_types=1);

namespace Mantas6\FzfPhp;

use Closure;
use Exception;
use Symfony\Component\Process\Process;

/**
 * @internal
 */
class Socket
{
    private $socket;
    private string $path;

    public function start(): string
    {
        $this->path = $this->generateSocketPath();

        $this->socket = stream_socket_server('unix://'.$this->path, $errno, $errstr);

        return $this->path;
    }

    public function stop(): void
    {
        stream_socket_shutdown($this->socket, STREAM_SHUT_RDWR);

        if (file_exists($this->path)) {
            unlink($this->path);
        }
    }

    public function listen(Closure $handler): void
    {
        $read = [$this->socket];
        $write = [];

        if (in_array(stream_select($read, $write, $write, 0), [0, false], true)) {
            return;
        }

        $conn = stream_socket_accept($this->socket, 0.1);

        if ($conn === false) {
            return;
        }

        $request = '';

        while (!feof($conn)) {
            $request .= fgets($conn, 1024);
        }

        $response = $handler($request);

        fwrite($conn, (string) $response);

        fclose($conn);
    }

    private function generateSocketPath(): string
    {
        exec('mktemp -u', $path, $exitCode);

        if ($exitCode !== 0) {
            throw new Exception('Failed to reserve a socket tmp file');
        }

        $path = array_shift($path) . '-fzf-php';

        return $path;
    }
}
