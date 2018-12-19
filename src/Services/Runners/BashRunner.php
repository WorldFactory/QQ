<?php

namespace WorldFactory\QQ\Services\Runners;

use Exception;
use Symfony\Component\Process\Process;

class BashRunner extends AbstractRunner
{
    protected function createProcess($script)
    {
        return new Process($script, null, $_ENV);
    }

    protected function getProcess(string $script)
    {
        $process = $this->createProcess($script);

        $process
            ->setTimeout(0)
            ->setIdleTimeout(0)
            ->setTty(Process::isPtySupported())
        ;

        $config = $this->getCommand()->getConfig();
        if (array_key_exists('workingDir', $config)) {
            $process->setWorkingDirectory($config['workingDir']);
        }

        return $process;
    }

    /**
     * @param string $script
     * @throws \Exception
     */
    public function run(string $script)
    {
        /** @var Process $process */
        $process = $this->getProcess($script);

        $process->run(function ($type, $buffer) {
            echo $buffer.PHP_EOL;
        });

        if (!$process->isSuccessful()) {
            $exception = new Exception("Unknown system error : '{$process->getExitCode()}' for command :  \"{$script}\"");

            throw $exception;
        }
    }
}