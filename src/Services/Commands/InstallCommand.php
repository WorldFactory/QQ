<?php

namespace WorldFactory\QQ\Services\Commands;

use Symfony\Bundle\FrameworkBundle\Command\AboutCommand as ParentCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class InstallCommand extends ParentCommand
{
    protected static $defaultName = 'about';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Install QQ files')
            ->setHelp(<<<'EOT'
The <info>%command.name%</info> command files used by QQ.
EOT
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
    }
}
