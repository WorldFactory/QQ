<?php

namespace WorldFactory\QQ\Services\Commands;

use WorldFactory\QQ\Components\Hosts;
use WorldFactory\QQ\Services\HostsHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RemoveHostCommand extends Command
{
    protected static $defaultName = 'host:remove';

    /** @var HostsHandler */
    private $hostsHandler;

    /**
     * @param HostsHandler $hostsHandler
     */
    public function setHostsHandler(HostsHandler $hostsHandler): void
    {
        $this->hostsHandler = $hostsHandler;
    }

    protected function configure()
    {
        $this
            ->setDescription('Add new entry in hosts file.')
            ->addArgument('name', InputArgument::REQUIRED, 'Host name.')
            ->addOption('target', 'f', InputOption::VALUE_OPTIONAL, 'Target host file path.', '/etc/hosts')
            ->setHelp('This command allows you to remove entry in /etc/hosts file...')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $target = $input->getOption('target');
        $name = $input->getArgument('name');

        $hosts = new Hosts($target);

        $hosts->removeHost($name);

        $output->writeln("Saving '$target' file. If the file is protected, you may need to enter your password.");

        $result = $this->hostsHandler->saveHosts($hosts);

        if ($result) {
            $output->writeln("<info>'$target' rewrited successfully.</info>");
        } else {
            $output->writeln("<error>An error occured when rewriting '$target'.</error>");
        }

        return $result ? 0 : 1;
    }
}