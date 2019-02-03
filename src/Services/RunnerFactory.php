<?php

namespace WorldFactory\QQ\Services;

use Exception;
use WorldFactory\QQ\Interfaces\RunnerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class RunnerFactory
{
    const PROTOCOL_REGEX = "/^(?<header>(?<type>[A-Z0-9_\.-]+):\/\/)/iJ";

    private $runnerServiceNames = [];

    private $runnerAliases = [];

    /** @var ContainerInterface  */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $runnerServiceName
     * @param string $name
     */
    public function addRunnerDefinition(string $runnerServiceName, string $name, string $alias = null)
    {
        $this->runnerServiceNames[$name] = $runnerServiceName;

        if ($alias !== null) {
            $this->runnerAliases[$alias] = $name;
        }
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
}