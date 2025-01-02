<?php

namespace Mantas6\FzfPhp\Support;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

/**
* @internal
*/
class CompactTable
{
    public function __construct(
        private readonly OutputInterface $output,
    ) {
        //
    }

    public function display(array $rows): void
    {
        $table = new Table($this->output);

        $table->setStyle('compact')
            ->setRows($rows)
            ->render();
    }
}
