<?php

namespace WorldFactory\QQ\Services\Runners;

use Exception;
use Symfony\Component\Process\Process;
use WorldFactory\QQ\Foundations\AbstractRunner;

class ShellRunner extends AbstractRunner
{
    const OPTION_DEFINITIONS = [
        'workingDir' => [
            'type' => 'string',
            'description' => "The working directory for the executed script."
        ]
    ];

    protected const SHORT_DESCRIPTION = "Run script in CLI.";

    protected const LONG_DESCRIPTION = <<<EOT
Run script using Symfony\Process class.
Many options are available.
This is the cleanest way to run a script with QQ.
EOT;

    protected function createProcess(string $script)
    {
        return Process::fromShellCommandline($script, null, $_ENV, $this->getInput()->getStream());
    }

    protected function getProcess(string $script)
    {
        $process = $this->createProcess($script);

        $process
            ->setTimeout(0)
            ->setIdleTimeout(0)
            ->setTty(Process::isPtySupported())
        ;

        if ($this->hasOption('workingDir')) {
            $process->setWorkingDirectory($this->getOption('workingDir'));
        }

        return $process;
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function execute(string $script) : void
    {
        /** @var Process $process */
        $process = $this->getProcess($script);

        $process->run([$this, 'displayCallback']);

        if (!$process->isSuccessful()) {
            $exception = new Exception("Unknown system error : '{$process->getExitCode()}' for command :  \"{$script}\"");

            throw $exception;
        }
    }

    public function displayCallback ($type, $buffer) {
        $this->getOutput()->writeln($buffer);
    }
}