<?php

namespace WorldFactory\QQ\Services;

use Exception;
use WorldFactory\QQ\Interfaces\RunnerInterface;
use WorldFactory\QQ\Interfaces\TokenizedInputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class RunnerFactory
{
    const PROTOCOL_REGEX = "/^(?<header>(?<type>[A-Z0-9_\.-]+):\/\/)/iJ";

    private $runnerServiceNames = [];

    private $runnerAliases = [];

    /** @var ContainerInterface */
    private $container;

    /** @var array List of deprecated aliases */
    private $deprecatedAliases = [];

    /** @var TokenizedInputInterface */
    private $input;

    /** @var OutputInterface */
    private $output;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $runnerServiceName
     * @param string $name
     * @param string $alias
     * @param bool $deprecated
     */
    public function addRunnerDefinition(string $runnerServiceName, string $name, string $alias = null, bool $deprecated = false)
    {
        $this->runnerServiceNames[$name] = $runnerServiceName;

        if ($alias !== null) {
            $this->runnerAliases[$alias] = $name;

            if ($deprecated) {
                $this->deprecatedAliases[] = $alias;
            }
        }
    }

    public function setInputOutput(TokenizedInputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
    }

    /**
     * @param string $name
     * @return string
     * @throws Exception
     */
    public function getRunnerServiceName($name) : string
    {
        $name = $this->getRealRunnerName($name);

        if (!$this->hasRunner($name)) {
            throw new Exception("Unknown runner type : '$name'.");
        }

        return array_key_exists($name, $this->runnerServiceNames) ? $this->runnerServiceNames[$name] : $this->runnerAliases[$name];
    }

    public function getRealRunnerName(string $name)
    {
        if (!array_key_exists($name, $this->runnerServiceNames) && array_key_exists($name, $this->runnerAliases)) {
            $name = $this->runnerAliases[$name];
        }

        return $name;
    }

    /**
     * @param string $name
     * @return RunnerInterface
     * @throws Exception
     */
    public function getRunner($name) : RunnerInterface
    {
        if ($this->isDeprecated($name)) {
            trigger_error("'$name' Runner alias is deprecated. Consider to use new Runner name.", E_USER_DEPRECATED);
        }

        $name = $this->getRealRunnerName($name);

        $serviceName = $this->getRunnerServiceName($name);

        /** @var RunnerInterface $runner */
        $runner = $this->container->get($serviceName);

        return $runner;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getRunners() : array
    {
        $runners = [];

        foreach (array_keys($this->runnerServiceNames) as $runnerName) {
            $runners[$runnerName] = $this->getRunner($runnerName);
        }

        return $runners;
    }

    public function getRunnerAliases(string $name) : array
    {
        $name = $this->getRealRunnerName($name);

        return array_keys($this->runnerAliases, $name);
    }

    public function hasRunner(string $name)
    {
        $name = $this->getRealRunnerName($name);

        return (array_key_exists($name, $this->runnerServiceNames));
    }

    public function isDeprecated(string $name)
    {
        return in_array($name, $this->deprecatedAliases);
    }
}