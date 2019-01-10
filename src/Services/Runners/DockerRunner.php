<?php

namespace WorldFactory\QQ\Services\Runners;

use Exception;
use Symfony\Component\Process\Process;
use WorldFactory\QQ\Interfaces\RunnerInterface;
use WorldFactory\QQ\Services\RunnerFactory;

class DockerRunner extends AbstractRunner
{
    /** @var RunnerFactory */
    private $runnerFactory;

    /**
     * @param RunnerFactory $runnerFactory
     */
    public function setRunnerFactory(RunnerFactory $runnerFactory) : void
    {
        $this->runnerFactory = $runnerFactory;
    }

    /**
     * @param string $script
     * @throws Exception
     */
    public function run(string $script) : void
    {
        $config = $this->getCommand()->getConfig();

        if (!array_key_exists('target', $config)) {
            throw new \InvalidArgumentException("You should define target container with 'target' parameter.");
        }

        /** @var string $target */
        $target = $config['target'];

        /** @var RunnerInterface $runner */
        $runner = null;

        if ($this->isUnix()) {
            $runner = $this->runnerFactory->getRunner('bash');

            $dockerScript = "docker-compose exec $target $script";
        } else {
            $runner = $this->runnerFactory->getRunner('exec');

            $dockerScript = "docker-compose exec -T $target $script";
        }

        $runner
            ->setCommand($this->getCommand())
            ->setOutput($this->getOutput())
            ->run($script)
        ;
    }

    /**
     * @return bool
     */
    protected function isUnix()
    {
        return PATH_SEPARATOR === ':';
    }
}