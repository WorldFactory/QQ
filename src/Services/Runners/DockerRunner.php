<?php

namespace WorldFactory\QQ\Services\Runners;

use Exception;
use Symfony\Component\Process\Process;
use WorldFactory\QQ\Interfaces\RunnerInterface;
use WorldFactory\QQ\Services\RunnerFactory;
use WorldFactory\QQ\Services\VarFormatter;

class DockerRunner extends AbstractRunner
{
    /** @var RunnerFactory */
    private $runnerFactory;

    /** @var VarFormatter */
    private $varFormatter;

    /**
     * @param RunnerFactory $runnerFactory
     */
    public function setRunnerFactory(RunnerFactory $runnerFactory) : void
    {
        $this->runnerFactory = $runnerFactory;
    }

    /**
     * @param VarFormatter $varFormatter
     */
    public function setVarFormatter($varFormatter)
    {
        $this->varFormatter = $varFormatter;
    }

    public function format(string $script) : string
    {
        $config = $this->getCommand()->getConfig();

        if (!array_key_exists('target', $config)) {
            throw new \InvalidArgumentException("You should define target container with 'target' parameter.");
        }

        /** @var string $target */
        $target = $config['target'];

        if ($this->isUnix()) {
            $dockerScript = "docker-compose exec $target $script";
        } else {
            $dockerScript = "docker-compose exec -T $target $script";
        }

        return $dockerScript;
    }

    /**
     * @param string $script
     * @throws Exception
     */
    public function run(string $script) : void
    {
        /** @var RunnerInterface $runner */
        $runner = null;

        if ($this->isUnix()) {
            $runner = $this->runnerFactory->getRunner('bash');
        } else {
            $runner = $this->runnerFactory->getRunner('exec');
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