<?php

namespace WorldFactory\QQ\Services\Runners;

use Exception;
use Symfony\Component\Process\Process;
use WorldFactory\QQ\Entities\Script;

class BashRunner extends AbstractRunner
{
    const OPTION_DEFINITIONS = [
        'workingDir' => ['type' => 'string']
    ];

    protected const SHORT_DESCRIPTION = "Run script in CLI.";

    protected const LONG_DESCRIPTION = <<<EOT
Run script using Symfony\Process class.
Many options are available.
This is the cleanest way to run a script with QQ.
EOT;

    protected function createProcess(Script $script)
    {
        return Process::fromShellCommandline($script->getCompiledScript(), null, $_ENV, $this->getInput()->getStream());
    }

    protected function getProcess(Script $script)
    {
        $process = $this->createProcess($script);

        $process
            ->setTimeout(0)
            ->setIdleTimeout(0)
            ->setTty(Process::isPtySupported())
        ;

        if ($script->hasOption('workingDir')) {
            $process->setWorkingDirectory($script->getOption('workingDir'));
        }

        return $process;
    }

    /**
     * @param string $script
     * @throws \Exception
     */
    public function run(Script $script) : void
    {
        /** @var Process $process */
        $process = $this->getProcess($script);

        $process->run([$this, 'displayCallback']);

        if (!$process->isSuccessful()) {
            $exception = new Exception("Unknown system error : '{$process->getExitCode()}' for command :  \"{$script->getCompiledScript()}\"");

            throw $exception;
        }
    }

    public function displayCallback ($type, $buffer) {
        $this->getOutput()->writeln($buffer);
    }
}