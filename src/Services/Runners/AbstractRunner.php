<?php

namespace WorldFactory\QQ\Services\Runners;

use WorldFactory\QQ\Misc\BasicCommand;
use WorldFactory\QQ\Application;
use WorldFactory\QQ\Interfaces\RunnerInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractRunner implements RunnerInterface
{
    /** @var OutputInterface */
    private $output;

    /** @var Application */
    private $application;

    /** @var BasicCommand */
    private $command;

    public function __construct()
    {
    }

    public function format(string $script) : string
    {
        return $script;
    }

    public function setOutput(OutputInterface $output) : RunnerInterface
    {
        $this->output = $output;

        return $this;
    }

    public function getOutput() : OutputInterface
    {
        return $this->output;
    }

    public function setApplication(Application $application) : RunnerInterface
    {
        $this->application = $application;

        return $this;
    }

    public function getApplication() : Application
    {
        return $this->application;
    }

    public function setCommand(BasicCommand $command) : RunnerInterface
    {
        $this->command = $command;

        return $this;
    }

    public function getCommand() : BasicCommand
    {
        return $this->command;
    }
}