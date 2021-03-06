<?php

namespace WorldFactory\QQ\Services\Runners;

use Exception;
use Symfony\Component\Process\Process;
use WorldFactory\QQ\Foundations\AbstractRunner;
use WorldFactory\QQ\Misc\Buffer;

class ShellRunner extends AbstractRunner
{
    const OPTION_DEFINITIONS = [
        'workingDir' => [
            'type' => 'string',
            'description' => "The working directory for the executed script."
        ],
        'tty'      => [
            'type' => 'bool',
            'description' => "Use TTY to launch script.",
            'default' => false
        ],
        'trim' => [
            'type' => 'bool',
            'description' => "Trim result if it's a string.",
            'default' => true
        ],
        'env'        => [
            'type' => 'array',
            'description' => "Environment variables that must be injected.",
            'default' => []
        ]
    ];

    protected const SHORT_DESCRIPTION = "Run script in CLI.";

    protected const LONG_DESCRIPTION = <<<EOT
Run script using Symfony\Process class.
Many options are available.
This is the cleanest way to run a script with QQ.
EOT;

    /** @var Buffer The result of the command */
    private $buffer;

    public function __construct()
    {
        $this->buffer = new Buffer();
    }

    protected function createProcess(string $script)
    {
        $options = $this->getOptions();

        $env = array_merge($_ENV, $options['env']);

        return Process::fromShellCommandline($script, null, $env, $this->getInput()->getStream());
    }

    protected function getProcess(string $script)
    {
        $options = $this->getOptions();

        $process = $this->createProcess($script);

        $process
            ->setTimeout(0)
            ->setIdleTimeout(0)
            ->setTty($options['tty'])
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
    public function execute(string $script)
    {
        $options = $this->getOptions();

        $this->buffer->reset();

        /** @var Process $process */
        $process = $this->getProcess($script);

        $process->run([$this, 'displayCallback']);

        if (!$process->isSuccessful()) {
            throw new Exception("Unknown system error : '{$process->getExitCode()}' for command : {$script}");
        }

        $result = $this->buffer->get();

        return (is_string($result) && $options['trim']) ? trim($result) : $result;
    }

    public function displayCallback ($type, $buffer)
    {
        $this->getOutput()->write($buffer);

        if ($type === Process::OUT) {
            $this->buffer->add($buffer);
        }
    }
}