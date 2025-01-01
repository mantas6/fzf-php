<?php

namespace Mantas6\FzfPhp\Formatters;

use Mantas6\FzfPhp\Concerns\Formatter;

class NullFormatter implements Formatter
{
    /**
     * @param  array<string, string|bool>  $arguments
     * @return array<string, string|bool>
     */
    public function arguments(array $arguments): array
    {
        return $arguments;
    }

    /**
     * @param  array<mixed>  $options
     * @return array<string, string>
     */
    public function before(array $options): array
    {
        return $options;
    }

    /**
     * @param  array<string>  $selected
     * @return array<mixed>
     */
    public function after(array $selected): array
    {
        return $selected;
    }
}
