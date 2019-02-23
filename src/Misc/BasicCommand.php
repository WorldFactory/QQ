<?php

namespace WorldFactory\QQ\Misc;

use Exception;
use function get_class;
use WorldFactory\QQ\Entities\Script;
use WorldFactory\QQ\Entities\ScriptConfig;
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

class BasicCommand extends Command implements ContainerAwareInterface
{
    /** @var string|array Script ou liste des scripts à éxecuter. */
    private $script;

    /** @var string Type of the script. */
    private $defaultType;

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

        $this->script = $config['script'];

        $this->defaultType = $config['type'] ?? 'shell';

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
            throw new \BadMethodCallException("BasicCommand::execute only supporte TokenizedInputInterface.");
        }

        $this->input = $input;
        $this->output = $output;

        /** @var Script $localScript */
        $localScript = $this->buildScript();

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

//        $this->displayHeader = true;

        /** @var Script $script */
        foreach ($this->getIterator($localScript) as $script) {
            $this->executeScript($script);
        }

        if ($this->displayHeader) {
            /** @var DeprecationHandler $deprecationHandler */
            $deprecationHandler = $this->container->get('qq.handler.deprecation');

            if (count($deprecationHandler->getDeprecations()) > 0) {
                $output->writeln("Several depreciation messages were generated. Remember to change your code to make it easier for you to upgrade to the higher version of QQ.");
                foreach ($deprecationHandler->getDeprecations() as $deprecation) {
                    $output->writeln("<error>* $deprecation</error>");
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
            $this->script,
            $this->config['type'] ?? $this->defaultType,
            $this->input->getArgumentTokens(),
            new ScriptConfig($this->config['options'] ?? [], $this->config)
        );
    }

    protected function getIterator(Script $script) : \RecursiveArrayIterator
    {
        $iterator = $script->getIterator();

        $iterator = is_array($iterator) ? $iterator : [$iterator];

        return new \RecursiveArrayIterator($iterator);
    }

    /**
     * @param string $script
     * @throws Exception
     */
    protected function executeScript(Script $script)
    {
        /** @var ScriptFormatterInterface $formatter */
        $formatter = $this->container->get('qq.formatter.script');

        $formatter->setTokens($script->getTokens());

        /** @var RunnerInterface */
        $runner = $this->findRunner($script)
            ->setCommand($this, true)
            ->setVarFormatter($formatter)
            ->setInput($this->input)
            ->setOutput($this->output)
        ;

        $script->setFormatter($formatter);
        $script->setRunner($runner);

        $script->compile();

        if ($this->output->isVerbose()) {
            $class = get_class($runner);
            $this->output->writeln("-> Runner : <fg=magenta>{$class}</>");
        }

        if ($runner->isHeaderDisplayed()) {
            $this->output->writeln("-> <fg=black;bg=green>{$script->getCompiledScript()}</>");
        }

        $script->execute();
    }

    /**
     * @param string $script
     * @return RunnerInterface
     * @throws Exception
     */
    protected function findRunner(Script $script) : RunnerInterface
    {
        /** @var string $type */
        $type = $this->defaultType;

        if (preg_match(RunnerFactory::PROTOCOL_REGEX, $script->getScript(), $result)) {
            $type = $result['type'];
        }

        /** @var RunnerFactory $runnerFactory */
        $runnerFactory = $this->container->get('qq.factory.runners');

        /** @var RunnerInterface $runner */
        $runner = $runnerFactory->getRunner($type);

        if ($runnerFactory->isDeprecated($type)) {
            trigger_error("'$type' Runner alias is deprecated. Consider to use Runner real name.", E_USER_DEPRECATED);
        }

        return $runner;
    }
}
