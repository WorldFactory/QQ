<?php

namespace WorldFactory\QQ\Services\Runners;

use Exception;
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
        $target = $this->varFormatter->format($config['target']);

        /** @var array $parameters */
        $parameters = [];

        if (array_key_exists('user', $config)) {
            $parameters[] = "--user=" . $this->varFormatter->format($config['user']);
        }

        if (array_key_exists('env', $config)) {
            $parameters[] = "--env=" . $this->varFormatter->format($config['env']);
        }

        if (array_key_exists('workingDir', $config)) {
            $parameters[] = "--workdir=" . $this->varFormatter->format($config['workingDir']);
        }

        if (array_key_exists('flags', $config) && is_array($config['flags'])) {
            $flags = $config['flags'];

            if (array_search('detach', $flags)) {
                $parameters[] = "--detach";
            }

            if (array_search('interactive', $flags)) {
                $parameters[] = "--interactive";
            }

            if (array_search('privilegied', $flags)) {
                $parameters[] = "--privilegied";
            }
        }

        if (!$this->isUnix()) {
            $parameters[] = "--tty";
        }

        $execArgs = join(' ', $parameters);

        return "docker-compose exec $execArgs $target $script";
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