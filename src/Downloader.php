<?php

namespace Mantas6\FzfPhp;

use Composer\Autoload\ClassLoader;
use Exception;
use PharData;
use Symfony\Component\Process\InputStream;
use Symfony\Component\Process\Process;


class Downloader
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        // GitHub API URL for fzf releases
        $apiUrl = "https://api.github.com/repos/junegunn/fzf/releases/latest";

        // Function to get the system's architecture
        function getSystemArchitecture()
        {
            $uname = php_uname('m');

            switch ($uname) {
                case 'x86_64':
                    return 'amd64';
                case 'i386':
                case 'i686':
                    return '386';
                case 'armv7l':
                    return 'armv7';
                case 'aarch64':
                    return 'arm64';
                default:
                    throw new Exception("Unsupported architecture: $uname");
            }
        }

        // Fetch the latest release data from GitHub
        function fetchLatestRelease($url)
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'User-Agent: PHP Script'
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
        function downloadFileWithProgressBar($url, $destination)
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
                if (strpos($asset['name'], $arch) !== false && strpos($asset['name'], 'linux') !== false) {
                    $downloadUrl = $asset['browser_download_url'];
                    break;
                }
            }

            if (!$downloadUrl) {
                throw new Exception("No suitable binary found for architecture: $arch");
            }

            // Download the binary with progress bar
            $binaryFile = basename($downloadUrl);
            echo "Downloading $binaryFile..." . PHP_EOL;
            downloadFileWithProgressBar($downloadUrl, $binaryFile);
            echo "\nDownloaded $binaryFile successfully." . PHP_EOL;

            // Extract the tar archive
            $phar = new PharData($binaryFile);
            $extractDir = './vendor/bin';
            $phar->extractTo($extractDir, overwrite: true);
            echo "Extracted $binaryFile to $extractDir successfully." . PHP_EOL;
            unlink($binaryFile);
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . PHP_EOL;
        }
    }
}

