<?php

namespace Mantas6\FzfPhp\Concerns;

interface Formatter
{
    /**
     * Maps arguments of the command
     *
     * @param  array<string, string|bool>  $arguments
     * @return array<string, string|bool>
     */
    public function arguments(array $arguments): array;

    /**
     * @param  array<mixed>  $options
     * @return array<string, string>
     */
    public function before(array $options, array $arguments): array;

    /**
     * @param  array<string>  $selected
     * @return array<mixed>
     */
    public function after(array $selected, array $arguments): array;
}
