<?php

namespace WorldFactory\QQ\Services\Commands;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use WorldFactory\QQ\Interfaces\RunnerInterface;
use WorldFactory\QQ\Services\RunnerFactory;

class RunnerListCommand extends Command implements ContainerAwareInterface
{
    /** @var ContainerInterface */
    private $container;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Display list of QQ Runners.')
            ->setHelp(<<<'EOT'
The <info>%command.name%</info> command display the list of currently activated QQ Runners.
EOT
            )
        ;
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var RunnerFactory $runnerFactory */
        $runnerFactory = $this->container->get('qq.factory.runners');

        $output->writeln('List of currently activated QQ Runners :');

        $table = new Table($output);
        $table->setHeaders(['Name', 'Description']);

        /**
         * @var string $runnerName
         * @var RunnerInterface $runner
         */
        foreach($runnerFactory->getRunners() as $runnerName => $runner) {
            $table->addRow([
                $runnerName,
                $runner->getShortDescription()
            ]);
        }

        $table->render();
    }
}
