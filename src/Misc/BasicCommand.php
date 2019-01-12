<?php

namespace WorldFactory\QQ\Misc;

use function array_keys;
use Exception;
use function get_class;
use WorldFactory\QQ\Services\RunnerFactory;
use WorldFactory\QQ\Application;
use WorldFactory\QQ\Interfaces\RunnerInterface;
use function mt_rand;
use function preg_match;
use function round;
use function str_replace;
use function strlen;
use function substr;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use WorldFactory\QQ\Services\VarFormatter;

class BasicCommand extends Command implements ContainerAwareInterface
{
    /** @var string|array Script ou liste des scripts à éxecuter. */
    private $script;

    /** @var string Type of the script. */
    private $defaultType;

    /** @var array Configuration de la commande. */
    private $config = [];

    /** @var InputInterface */
    private $input;

    /** @var OutputInterface */
    private $output;

    /** @var bool */
    private $displayHeader = true;

    /** @var ContainerInterface */
    private $container;

    /**
     * @param string|null $name The name of the command; passing null means it must be set in configure()
     *
     * @throws LogicException When the command name is empty
     */
    public function __construct(array $config)
    {
        $this->config = $config;

        $this->script = $config['script'];

        $this->defaultType = $config['type'] ?? 'bash';

        parent::__construct($this->config['name']);
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @return OutputInterface
     */
    public function getOutput(): OutputInterface
    {
        return $this->output;
    }

    /**
     * @return InputInterface
     */
    public function getInput(): InputInterface
    {
        return $this->input;
    }

    protected function configure()
    {
        $this
            ->setName($this->config['name'])
            ->setDescription($this->config['shortDescription'] ?? null)
            ->setHelp($this->config['longDescription'] ?? null)
            ->setAliases($this->config['aliases'] ?? [])
            ->ignoreValidationErrors()
        ;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function setDisplayHeader (bool $displayHeader)
    {
        $this->displayHeader = $displayHeader;

        return $this;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws Exception
     * @throws \Throwable
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        if ($this->displayHeader) {

            $formatter = $this->getHelper('formatter');

            $this->output->writeln($formatter->formatSection(
                'Task name  ',
                $this->getName()
            ));

            if ($this->getDescription()) {
                $this->output->writeln($formatter->formatSection(
                    'Description',
                    $this->getDescription()
                ));
            }
        }

        $this->displayHeader = true;

        if (is_array($this->getScript())) {
            foreach ($this->getScript() as $script) {
                $this->executeScript($script);
            }
        } else {
            $this->executeScript($this->getScript());
        }

        return 0;
    }

    /**
     * @param string $script
     * @throws Exception
     */
    protected function executeScript($script)
    {
        /** @var VarFormatter $varFormatter */
        $varFormatter = $this->container->get('qq.formatter.var');

        $varFormatter->init($this->input);

        /** @var RunnerInterface */
        $runner = $this
            ->findRunner($script)
            ->setCommand($this)
            ->setVarFormatter($varFormatter)
            ->setOutput($this->output)
        ;

        if ($this->output->isVerbose()) {
            $class = get_class($runner);
            $this->output->writeln("-> Runner : <fg=magenta>{$class}</>");
        }

        $script = $varFormatter->sanitize($script);
        $script = $varFormatter->format($script);

        $script = $runner->format($script);

        $script = $varFormatter->finalize($script);

        if ($runner->isHeaderDisplayed()) {
            $this->output->writeln("-> <fg=black;bg=green>{$script}</>");
        }

        $runner->run($script)
        ;
    }

    protected function getScript()
    {
        return $this->script;
    }

    /**
     * @param string $script
     * @return RunnerInterface
     * @throws Exception
     */
    private function findRunner(string $script) : RunnerInterface
    {
        /** @var string $type */
        $type = $this->defaultType;

        if (preg_match(RunnerFactory::PROTOCOL_REGEX, $script, $result)) {
            $type = $result['type'];
        }

        /** @var RunnerFactory $runnerFactory */
        $runnerFactory = $this->container->get('qq.factory.runners');

        /** @var RunnerInterface $runner */
        $runner = $runnerFactory->getRunner($type);

        return $runner;
    }
}
