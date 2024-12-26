<?php

/**
 * This file is part of RoadRunner package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Mantas6\FzfPhp;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GetBinaryCommand extends Command
{
    public function __construct(?string $name = null)
    {
        parent::__construct($name ?? 'get-binary');
    }

    protected function configure(): void
    {
        Downloader::installLatestRelease();
    }

    /**
     * {@inheritDoc}
     */
    public function getDescription(): string
    {
        return 'Install or update Fzf binary';
    }

    /**
     * {@inheritDoc}
     *
     * @throws \Throwable
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        return 0;
    }
}
