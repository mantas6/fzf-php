<?php

declare(strict_types=1);

namespace Mantas6\FzfPhp\ValueObjects;

use Mantas6\FzfPhp\Socket;

final class State
{
    private array $availableOptions = [];
    private array $arguments = [];

    public function __construct(public Socket $socket = new Socket)
    {
        //
    }

    public static function prepareDefault(): static
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

    public function setArguments(array $arguments): self
    {
        $this->arguments = $arguments;

        return $this;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }
}
