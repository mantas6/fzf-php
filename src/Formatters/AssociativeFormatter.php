<?php

namespace Mantas6\FzfPhp\Formatters;

use Mantas6\FzfPhp\Concerns\Formatter;

class AssociativeFormatter implements Formatter
{
    /**
     * @param  array<string, string|bool>  $arguments
     * @return array<string, string|bool>
     */
    public function arguments(array $arguments): array
    {
        return [
            ...$arguments,
            'd' => false,
            'delimiter' => ':',
            'with-nth' => '2..',
        ];
    }

    /**
     * @param  array<mixed>  $options
     * @return array<string, string>
     */
    public function before(array $options, array $arguments): array
    {
        $processed = [];

        foreach ($options as $key => $value) {
            $processed[] = $key . $arguments['delimiter'] . $value;
        }

        return $processed;
    }

    /**
     * @param  array<string>  $selected
     * @return array<mixed>
     */
    public function after(array $selected, array $arguments): array
    {
        $keys = [];

        foreach ($selected as $value) {
            $keys[] = substr(
                $value,
                0,
                strpos($value, (string) $arguments['delimiter']),
            );
        }

        return $keys;
    }
}
