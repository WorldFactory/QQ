<?php

namespace WorldFactory\QQ\Services\Commands;

use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use WorldFactory\QQ\Interfaces\RunnerInterface;
use WorldFactory\QQ\Services\RunnerFactory;

class RunnerHelpCommand extends Command implements ContainerAwareInterface
{
    /** @var ContainerInterface */
    private $container;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, "The name of the runner whose help you want to display.")
            ->setDescription('Display help for a selected Runner.')
            ->setHelp(<<<'EOT'
The <info>%command.name%</info> command displays the help available for the selected command.
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
        $name = $input->getArgument('name');

        /** @var RunnerFactory $runnerFactory */
        $runnerFactory = $this->container->get('qq.factory.runners');

        $name = $runnerFactory->getRealRunnerName($name);

        if ($runnerFactory->hasRunner($name)) {
            $runner = $runnerFactory->getRunner($name);

            $formatter = new FormatterHelper();

            $message = $formatter->formatBlock(
                "Help available on the '$name' Runner :",
                'fg=yellow;options=bold',
                TRUE
            );

            $output->writeln($message);

            $table = new Table($output);

            $table->addRows([
                ["<info>Name</info>", $name],
                ["<info>Aliases</info>", implode(', ', $runnerFactory->getRunnerAliases($name))],
                ["<info>Description</info>", $runner->getShortDescription()],
                ["<info>Class</info>", get_class($runner)],
                ["<info>Service</info>", $runnerFactory->getRunnerServiceName($name)]
            ]);

            $table->render();

            $output->writeln("Long desription :");
            $output->writeln($runner->getLongDescription());

            $optionDefinitions = $runner->getOptionDefinitions();

            if (!empty($optionDefinitions)) {
                $table = new Table($output);
                $table->setHeaders(['Option', 'Type', 'Required', 'Default', 'Description']);

                /**
                 * @var string $runnerName
                 * @var RunnerInterface $runner
                 */
                foreach ($optionDefinitions as $optionName => $optionDefinition) {
                    $default = $optionDefinition['default'] ?? '';

                    $table->addRow([
                        $optionName,
                        $optionDefinition['type'] ?? 'undefined',
                        ($optionDefinition['required'] ?? false) ? 'yes' : 'no',
                        is_scalar($default) ? $default : 'A non-scalar value',
                        $optionDefinition['description'] ?? ''
                    ]);
                }

                $table->render();
            } else {
                $output->writeln("This runner does not have any options.");
            }
        } else {
            $output->writeln("<error>Runner '$name' was not found</error>");
        }
    }
}
