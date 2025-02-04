<?php

namespace Mantas6\FzfPhp\ValueObjects;

class State
{
    private array $availableOptions = [];

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
