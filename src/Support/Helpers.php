<?php

declare(strict_types=1);

namespace Mantas6\FzfPhp\Support;

use Composer\Autoload\ClassLoader;

final readonly class Helpers
{
    /**
     * Gets the path of the project.
     */
    public static function basePath(): string
    {
        return dirname(
            array_keys(ClassLoader::getRegisteredLoaders())[0]
        );
    }

    public static function alwaysArray(mixed $value): array
    {
        return match (is_array($value)) {
            true => $value,
            false => [$value],
        };
    }
}
