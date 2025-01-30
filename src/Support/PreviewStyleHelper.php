<?php

declare(strict_types=1);

namespace Mantas6\FzfPhp\Support;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

class PreviewStyleHelper
{
    private readonly BufferedOutput $output;
    private readonly SymfonyStyle $style;

    public function __construct()
    {
        $this->output = new BufferedOutput(decorated: true);
        $this->style = new SymfonyStyle(new StringInput(''), $this->output);
    }

    public function table(array $headers = [], array $rows = []): self
    {
        $table = new Table($this->output);

        $table->setStyle('compact')
            ->setHeaders($headers)
            ->setRows($rows)
            ->render();

        return $this;
    }

    public function block(...$args): self
    {
        $this->style->block(...$args);

        return $this;
    }

    public function render(): string
    {
        return $this->output->fetch();
    }
}
