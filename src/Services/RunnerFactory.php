<?php

namespace WorldFactory\QQ\Services;

use Exception;
use WorldFactory\QQ\Interfaces\RunnerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class RunnerFactory
{
    const PROTOCOL_REGEX = "/^(?<header>(?<type>[A-Z0-9_\.-]+):\/\/)/iJ";

    private $runnerServiceNames = [];

    private $runnerServiceAliases = [];

    /** @var ContainerInterface  */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $runnerServiceName
     * @param string $type
     */
    public function addRunnerDefinition(string $runnerServiceName, string $type, string $alias = null)
    {
        $this->runnerServiceNames[$type] = $runnerServiceName;

        if ($alias !== null) {
            $this->runnerServiceAliases[$alias] = $runnerServiceName;
        }
    }

    /**
     * @param string $name
     * @return string
     * @throws Exception
     */
    public function getRunnerServiceName($name) : string
    {
        if (!array_key_exists($name, $this->runnerServiceNames) && !array_key_exists($name, $this->runnerServiceAliases)) {
            throw new Exception("Unknown runner type : '$name'.");
        }

        return array_key_exists($name, $this->runnerServiceNames) ? $this->runnerServiceNames[$name] : $this->runnerServiceAliases[$name];
    }

    /**
     * @param string $type
     * @return RunnerInterface
     * @throws Exception
     */
    public function getRunner($type) : RunnerInterface
    {
        $serviceName = $this->getRunnerServiceName($type);

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
        $serviceName = $this->getRunnerServiceName($name);

        return array_keys($this->runnerServiceAliases, $serviceName);
    }
}