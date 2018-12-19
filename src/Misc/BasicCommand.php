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

class BasicCommand extends Command implements ContainerAwareInterface
{
    const PROTOCOL_REGEX = "/^(?<header>(?<type>[A-Z0-9_\.-]+):\/\/)/iJ";

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
     * @param string|null $description
     * @throws Exception
     */
    protected function executeScript($script)
    {
        /** @var RunnerInterface */
        $runner = $this->findRunner($script);

        if ($this->output->isVerbose()) {
            $class = get_class($runner);
            $this->output->writeln("-> Runner : <fg=magenta>{$class}</>");
        }

        $script = $this->removeProtocol($script, $runner);
        $script = $this->injectEnvVars($script);
        $script = $this->injectParameters($script);
        $script = $this->injectArguments($script);

        $this->output->writeln("-> <fg=black;bg=green>{$script}</>");

        $script = $runner->format($script);

        $runner
            ->setApplication($this->getApplication())
            ->setCommand($this)
            ->setOutput($this->output)
            ->run($script)
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

        if (preg_match(self::PROTOCOL_REGEX, $script, $result)) {
            $type = $result['type'];
        }

        /** @var RunnerFactory $runnerFactory */
        $runnerFactory = $this->container->get('qq.factory.runners');

        /** @var RunnerInterface $runner */
        $runner = $runnerFactory->getRunner($type);

        return $runner;
    }

    public function removeProtocol(string $script) : string
    {
        if (preg_match(self::PROTOCOL_REGEX, $script, $result)) {
            $header = $result['header'];

            $script = substr($script, strlen($header));
        }

        return $script;
    }

    protected function injectArguments($script) : string
    {
        $tokens = $this->input->getSavedTokens();
        $index = array_search($this->input->getFirstArgument(), $tokens);
        $args = array_slice($tokens, $index + 1);

        $usedArgs = [];

        for ($c = 1; $c <= count($args); $c ++) {
            if (preg_match("/%$c%/", $script)) {
                $script = str_replace('%' . $c . '%', $args[$c - 1], $script);
                $usedArgs[] = $c;
            }
        }

        if (preg_match("/%_all%/", $script)) {
            $script = str_replace('%_all%', implode(' ', $args), $script);

            $usedArgs = array_keys($args);
            array_shift($usedArgs);
            $usedArgs[] = count($args);
        }

        if (preg_match("/%_left%/", $script)) {
            $leftArgs = [];
            foreach ($args as $key => $arg) {
                if (!in_array($key + 1, $usedArgs)) {
                    $leftArgs[] = $arg;
                }
            }
            $script = str_replace('%_left%', implode(' ', $leftArgs), $script);
        }

        return $script;
    }

    protected function injectParameters($script) : string
    {
        /** @var Application $application */
        $application = $this->getApplication();

        $parameters = $application->getConfigLoader()->getParameters();

        foreach ($parameters as $key => $val) {
            $script = str_replace('%' . $key . '%', $val, $script);
        }

        return $script;
    }

    protected function injectEnvVars($script) : string
    {
        foreach ($_ENV as $key => $val) {
            $script = str_replace('%ENV:' . $key . '%', $val, $script);
        }

        return $script;
    }
}
