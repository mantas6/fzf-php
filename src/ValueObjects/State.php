<?php

namespace Mantas6\FzfPhp\ValueObjects;

use Mantas6\FzfPhp\Socket;

class State
{
    private array $availableOptions = [];

    public function __construct(public Socket $socket = new Socket)
    {
        //
    }

    public static function default(): static
    {
        $state = new static;

        $state->socket->start();

        return $state;
    }

    public function setAvailableOptions(array $options): self
    {
        $this->availableOptions = $options;

        return $this;
    }

    public function getAvailableOptions(): array
    {
        return $this->availableOptions;
    }
}
