<?php

namespace WorldFactory\QQ\Services\Runners;

use Exception;
use WorldFactory\QQ\Entities\Script;
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

    public function format(Script $script, string $compiledScript) : string
    {
        $options = $script->getOptions();

        if (!isset($options['target'])) {
            throw new \InvalidArgumentException("You should define target container with 'target' parameter.");
        }

        /** @var string $target */
        $target = $options['target'];

        /** @var array $parameters */
        $parameters = [];

        if (isset($options['user'])) {
            $parameters[] = "--user=" . $options['user'];
        }

        if (isset($options['env'])) {
            $parameters[] = "--env=" . $options['env'];
        }

        if (isset($options['workingDir'])) {
            $parameters[] = "--workdir=" . $options['workingDir'];
        }

        if (isset($options['flags']) and is_array($options['flags'])) {
            if (in_array('detach', $options['flags'])) {
                $parameters[] = "--detach";
            }

            if (in_array('interactive', $options['flags'])) {
                $parameters[] = "--interactive";
            }

            if (in_array('privilegied', $options['flags'])) {
                $parameters[] = "--privilegied";
            }
        }

        if (!$this->isUnix()) {
            $parameters[] = "--tty";
        }

        $execArgs = join(' ', $parameters);

        return "docker-compose exec $execArgs $target $compiledScript";
    }

    /**
     * @param string $script
     * @throws Exception
     */
    public function run(Script $script) : void
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
            ->setVarFormatter($this->getVarFormatter())
            ->setInput($this->getInput())
            ->setOutput($this->getOutput())
        ;

        $script->setRunner($runner);

        $runner->run($script);
    }

    /**
     * @return bool
     */
    protected function isUnix()
    {
        return PATH_SEPARATOR === ':';
    }
}