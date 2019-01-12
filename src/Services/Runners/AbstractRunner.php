<?php

namespace WorldFactory\QQ\Services\Runners;

use WorldFactory\QQ\Misc\BasicCommand;
use WorldFactory\QQ\Application;
use WorldFactory\QQ\Interfaces\RunnerInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WorldFactory\QQ\Misc\ConfigLoader;
use WorldFactory\QQ\Services\VarFormatter;

abstract class AbstractRunner implements RunnerInterface
{
    /** @var OutputInterface */
    private $output;

    /** @var Application */
    private $application;

    /** @var BasicCommand */
    private $command;

    /** @var ConfigLoader */
    private $configLoader;

    /** @var VarFormatter */
    private $varFormatter;

    public function __construct(ConfigLoader $configLoader, Application $application)
    {
        $this->configLoader = $configLoader;
        $this->application = $application;
    }

    public function format(string $script) : string
    {
        return $script;
    }

    /**
     * @return ConfigLoader
     */
    public function getConfigLoader() : ConfigLoader
    {
        return $this->configLoader;
    }

    /**
     * @param VarFormatter $varFormatter
     */
    public function setVarFormatter(VarFormatter $varFormatter) : RunnerInterface
    {
        $this->varFormatter = $varFormatter;

        return $this;
    }

    /**
     * @return VarFormatter
     */
    public function getVarFormatter(): VarFormatter
    {
        return $this->varFormatter;
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

    public function isHeaderDisplayed() : bool
    {
        return true;
    }
}