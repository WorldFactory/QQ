<?php

namespace WorldFactory\QQ\Services;

use Exception;
use WorldFactory\QQ\Interfaces\RunnerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class RunnerFactory
{
    private $runnerServiceNames = [];

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
            $this->runnerServiceNames[$alias] = $runnerServiceName;
        }
    }

    /**
     * @param string $type
     * @return RunnerInterface
     * @throws Exception
     */
    public function getRunner($type) : RunnerInterface
    {
        if (!array_key_exists($type, $this->runnerServiceNames)) {
            throw new Exception("Unknown runner type : '$type'.");
        }

        /** @var RunnerInterface $runner */
        $runner = $this->container->get($this->runnerServiceNames[$type]);

        return $runner;
    }
}