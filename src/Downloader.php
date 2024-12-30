<?php

namespace Mantas6\FzfPhp;

use Exception;
use PharData;

class Downloader
{
    /**
     * Execute the console command.
     */
    public static function installLatestRelease(): void
    {
        // GitHub API URL for fzf releases
        $apiUrl = 'https://api.github.com/repos/junegunn/fzf/releases/latest';

        // Fetch the latest release data
        $releaseData = static::fetchLatestRelease($apiUrl);

        // Find the appropriate asset for the current architecture
        $assets = $releaseData['assets'] ?? [];
        $downloadUrl = null;

        foreach ($assets as $asset) {
            if (str_contains((string) $asset['name'], php_uname('m')) && str_contains((string) $asset['name'], strtolower(php_uname('s')))) {
                $downloadUrl = $asset['browser_download_url'];
                break;
            }
        }

        if (!$downloadUrl) {
            throw new Exception('No suitable binary found for the current system');
        }

        // Download the binary with progress bar
        $binaryFile = basename((string) $downloadUrl);
        echo "Downloading $binaryFile...".PHP_EOL;
        static::downloadFileWithProgressBar($downloadUrl, $binaryFile);
        echo "\nDownloaded $binaryFile successfully.".PHP_EOL;

        // Extract the tar archive
        $phar = new PharData($binaryFile);
        $extractDir = './vendor/bin';
        $phar->extractTo($extractDir, overwrite: true);
        echo "Extracted $binaryFile to $extractDir successfully.".PHP_EOL;
        unlink($binaryFile);
    }

    /**
    * @return null|array<string, array{assets: array<string, string>}>
    */
    protected static function fetchLatestRelease(string $url): ?array
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'User-Agent: PHP Script',
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception("Failed to fetch release data. HTTP Code: $httpCode");
        }

        /** @var array<string, array{assets: array<string, string>}> */
        $decoded = json_decode((string) $response, true);

        if ($decoded) {
            return (array) $decoded;
        }

        return null;
    }

    // Function to download a file with a progress bar
    protected static function downloadFileWithProgressBar(string $url, string $destination): void
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
