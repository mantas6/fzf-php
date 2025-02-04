<?php

declare(strict_types=1);

namespace Mantas6\FzfPhp\ValueObjects;

use Exception;
use Mantas6\FzfPhp\Enums\Action;

/**
 * @property-read Action $action
 * @property-read ?string $selection
 * @property-read FinderEnv $env
 */
final class SocketRequest
{
    public function __construct(
        private readonly Action $action,
        private readonly ?string $selection,
        private readonly FinderEnv $env,
    ) {
        //
    }

    public static function fromJson(string $json): static
    {
        $input = json_decode($json, true);

        $env = new FinderEnv($input['env']);

        return new static(
            action: Action::from($input['action']),
            selection: $input['selection'],
            env: $env,
        );
    }

    public function __get(string $name)
    {
        return match ($name) {
            'action' => $this->action,
            'selection' => $this->selection,
            'env' => $this->env,
            default => throw new Exception('Value does not exist '.$name),
        };
    }
}
