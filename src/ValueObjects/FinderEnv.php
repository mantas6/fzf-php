<?php

declare(strict_types=1);

namespace Mantas6\FzfPhp\ValueObjects;

use Symfony\Component\String\UnicodeString;

/**
 * @property-read string $lines Number of lines fzf takes up excluding padding and margin
 * @property-read string $columns Number of columns fzf takes up excluding padding and margin
 * @property-read string $totalCount Total number of items
 * @property-read string $matchCount Number of matched items
 * @property-read string $selectCount Number of selected items
 * @property-read string $pos Vertical position of the cursor in the list starting from 1
 * @property-read string $query Current query string
 * @property-read string $nth Current --nth option
 * @property-read string $prompt Prompt string
 * @property-read string $previewLabel Preview label string
 * @property-read string $borderLabel Border label string
 * @property-read string $action The name of the last action performed
 * @property-read string $key The name of the last key pressed
 * @property-read string $port Port number when –listen option is used
 * @property-read string $previewTop Top position of the preview window
 * @property-read string $previewLeft Left position of the preview window
 * @property-read string $previewLines Number of lines in the preview window
 * @property-read string $previewColumns Number of columns in the preview window
 */
class FinderEnv
{
    public function __construct(private array $env)
    {
        //
    }

    public function __get(string $name)
    {
        $key = (new UnicodeString($name))
            ->snake()
            ->prepend('fzf_')
            ->upper()
            ->toString();

        return $this->env[$key];
    }

    public function all(): array
    {
        return $this->env;
    }
}
