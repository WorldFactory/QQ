<?php

namespace WorldFactory\QQ\Services\Runners;

use Exception;
use WorldFactory\QQ\Interfaces\RunnerInterface;
use WorldFactory\QQ\Services\RunnerFactory;
use WorldFactory\QQ\Services\ScriptFormatter;

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

    public function format(string $script) : string
    {
        $config = $this->getCommand()->getConfig();

        if (!array_key_exists('target', $config)) {
            throw new \InvalidArgumentException("You should define target container with 'target' parameter.");
        }

        /** @var ScriptFormatter $formatter */
        $formatter = $this->getVarFormatter();

        /** @var string $target */
        $target = $formatter->format($config['target']);

        /** @var array $parameters */
        $parameters = [];

        if (array_key_exists('user', $config)) {
            $parameters[] = "--user=" . $formatter->format($config['user']);
        }

        if (array_key_exists('env', $config)) {
            $parameters[] = "--env=" . $formatter->format($config['env']);
        }

        if (array_key_exists('workingDir', $config)) {
            $parameters[] = "--workdir=" . $formatter->format($config['workingDir']);
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
            ->setInput($this->getInput())
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