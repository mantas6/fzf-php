<?php

declare(strict_types=1);

namespace Mantas6\FzfPhp;

use Exception;
use Mantas6\FzfPhp\Support\Helpers;
use PharData;

class Downloader
{
    protected static string $templateUrl = 'https://github.com/{repo}/releases/download/v{tag}/fzf-{tag}-{system}_{arch}.tar.gz';

    protected static string $repository = 'junegunn/fzf';
    protected static string $tag = '0.58.0';

    public static function installLatestRelease(): void
    {
        $downloadUrl = static::createDownloadUrl(
            system: strtolower(php_uname('s')),

            arch: match ($arch = php_uname('m')) {
                'x86_64' => 'amd64',
                default => $arch,
            }
        );

        $archive = basename($downloadUrl);
        static::downloadFile($downloadUrl, $archive);

        $basePath = Helpers::basePath();

        (new PharData($archive))->extractTo(
            directory: "$basePath/vendor/bin",
            overwrite: true,
        );

        unlink($archive);
    }

    protected static function createDownloadUrl(string $system, string $arch): string
    {
        $url = str_replace('{repo}', static::$repository, static::$templateUrl);
        $url = str_replace('{tag}', static::$tag, $url);
        $url = str_replace('{system}', $system, $url);

        return str_replace('{arch}', $arch, $url);
    }

    protected static function downloadFile(string $url, string $destination): void
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        curl_setopt($ch, CURLOPT_BUFFERSIZE, 128);

        $fileData = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception("Failed to download file. HTTP Code: $httpCode");
        }

        file_put_contents($destination, $fileData);
    }
}
