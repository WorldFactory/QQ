<?php

namespace WorldFactory\QQ\Misc;

use Exception;
use function get_class;
use WorldFactory\QQ\Entities\Accreditor;
use WorldFactory\QQ\Entities\Script;
use WorldFactory\QQ\Entities\RunnerConfig;
use WorldFactory\QQ\Interfaces\ScriptFormatterInterface;
use WorldFactory\QQ\Interfaces\TokenizedInputInterface;
use WorldFactory\QQ\Services\DeprecationHandler;
use WorldFactory\QQ\Services\RunnerFactory;
use WorldFactory\QQ\Interfaces\RunnerInterface;
use function preg_match;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use WorldFactory\QQ\Services\ScriptIterator;

class BasicCommand extends Command implements ContainerAwareInterface
{
    /** @var array Configuration de la commande. */
    private $config = [];

    /** @var TokenizedInputInterface */
    private $input;

    /** @var OutputInterface */
    private $output;

    /** @var bool */
    private $displayHeader = true;

    /** @var ContainerInterface */
    private $container;

    /**
     * BasicCommand constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;

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
     * @return TokenizedInputInterface
     */
    public function getInput(): TokenizedInputInterface
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
        if (!$input instanceof TokenizedInputInterface) {
            throw new \BadMethodCallException("BasicCommand::execute only support TokenizedInputInterface.");
        }

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

        /** @var ScriptIterator $scriptIterator */
        $scriptIterator = $this->container->get('qq.iterator.script');

        $scriptIterator->setInputOutput($this->input, $this->output);
        $scriptIterator->setCommand($this);

        /** @var Script $rootScript */
        $rootScript = $this->buildScript();

        /** @var Script $script */
        foreach ($scriptIterator->browse($rootScript) as $script) {
            $this->executeScript($script);
        }

        if ($this->displayHeader) {
            /** @var DeprecationHandler $deprecationHandler */
            $deprecationHandler = $this->container->get('qq.handler.deprecation');

            if (count($deprecationHandler->getDeprecations()) > 0) {
                $output->writeln("<error>Several deprecation messages were generated. Remember to change your code to make it easier for you to upgrade to the higher version of QQ.</error>");
                foreach ($deprecationHandler->getDeprecations() as $deprecation) {
                    $output->writeln("* $deprecation");
                }
            }
        }

        return 0;
    }

    /**
     * @return Script
     */
    protected function buildScript() : Script
    {
        return new Script(
            $this->config,
            'shell',
            $this->input->getArgumentTokens(),
            new RunnerConfig($this->config['options'] ?? [], $this->config)
        );
    }

    /**
     * @param string $script
     * @throws Exception
     */
    protected function executeScript(Script $script)
    {
        if ($this->output->isVerbose()) {
            $class = get_class($script->getRunner());
            $this->output->writeln("-> Runner : <fg=magenta>{$class}</>");
        }

        if ($script->getRunner()->isHeaderDisplayed()) {
            $this->output->writeln("-> <fg=black;bg=green>{$script->getCompiledScript()}</>");
        }

        $script->execute();
    }
}
