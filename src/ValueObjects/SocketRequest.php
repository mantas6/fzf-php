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
final readonly class SocketRequest
{
    public function __construct(
        private Action $action,
        private ?string $selection,
        private FinderEnv $env,
    ) {
        //
    }

    public static function fromJson(string $json): static
    {
        $input = json_decode($json, true);

        $env = new FinderEnv($input['env']);

        return new self(
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
