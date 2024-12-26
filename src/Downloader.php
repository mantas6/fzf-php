<?php

namespace Mantas6\FzfPhp;

use Exception;
use PharData;

class Downloader
{
    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        // GitHub API URL for fzf releases
        $apiUrl = 'https://api.github.com/repos/junegunn/fzf/releases/latest';

        // Function to get the system's architecture
        function getSystemArchitecture(): string
        {
            $uname = php_uname('m');

            return match ($uname) {
                'x86_64' => 'amd64',
                'i386', 'i686' => '386',
                'armv7l' => 'armv7',
                'aarch64' => 'arm64',
                default => throw new Exception("Unsupported architecture: $uname"),
            };
        }

        // Fetch the latest release data from GitHub
        function fetchLatestRelease($url): mixed
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

            return json_decode($response, true);
        }

        // Function to download a file with a progress bar
        function downloadFileWithProgressBar($url, $destination): void
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

        try {
            // Get system architecture
            $arch = getSystemArchitecture();

            // Fetch the latest release data
            $releaseData = fetchLatestRelease($apiUrl);

            // Find the appropriate asset for the current architecture
            $assets = $releaseData['assets'];
            $downloadUrl = null;

            foreach ($assets as $asset) {
                if (str_contains((string) $asset['name'], $arch) && str_contains((string) $asset['name'], 'linux')) {
                    $downloadUrl = $asset['browser_download_url'];
                    break;
                }
            }

            if (! $downloadUrl) {
                throw new Exception("No suitable binary found for architecture: $arch");
            }

            // Download the binary with progress bar
            $binaryFile = basename((string) $downloadUrl);
            echo "Downloading $binaryFile...".PHP_EOL;
            downloadFileWithProgressBar($downloadUrl, $binaryFile);
            echo "\nDownloaded $binaryFile successfully.".PHP_EOL;

            // Extract the tar archive
            $phar = new PharData($binaryFile);
            $extractDir = './vendor/bin';
            $phar->extractTo($extractDir, overwrite: true);
            echo "Extracted $binaryFile to $extractDir successfully.".PHP_EOL;
            unlink($binaryFile);
        } catch (Exception $e) {
            echo 'Error: '.$e->getMessage().PHP_EOL;
        }
    }
}
