<?php

namespace WorldFactory\QQ\Services\Runners;

use WorldFactory\QQ\Entities\Script;
use WorldFactory\QQ\Interfaces\ScriptFormatterInterface;
use WorldFactory\QQ\Interfaces\TokenizedInputInterface;
use WorldFactory\QQ\Misc\BasicCommand;
use WorldFactory\QQ\Application;
use WorldFactory\QQ\Interfaces\RunnerInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WorldFactory\QQ\Misc\ConfigLoader;

abstract class AbstractRunner implements RunnerInterface
{
    /** @var OutputInterface */
    private $output;

    /** @var TokenizedInputInterface */
    private $input;

    /** @var Application */
    private $application;

    /** @var BasicCommand */
    private $command;

    /** @var ConfigLoader */
    private $configLoader;

    /** @var ScriptFormatterInterface */
    private $varFormatter;

    public function __construct(ConfigLoader $configLoader, Application $application)
    {
        $this->configLoader = $configLoader;
        $this->application = $application;
    }

    public function format(Script $script, string $compiledScript) : string
    {
        return $compiledScript;
    }

    /**
     * @return ConfigLoader
     */
    public function getConfigLoader() : ConfigLoader
    {
        return $this->configLoader;
    }

    /**
     * @param ScriptFormatterInterface $varFormatter
     * @return RunnerInterface
     */
    public function setVarFormatter(ScriptFormatterInterface $varFormatter) : RunnerInterface
    {
        $this->varFormatter = $varFormatter;

        return $this;
    }

    /**
     * @return ScriptFormatterInterface
     */
    public function getVarFormatter(): ScriptFormatterInterface
    {
        return $this->varFormatter;
    }

    public function setInput(TokenizedInputInterface $input) : RunnerInterface
    {
        $this->input = $input;

        return $this;
    }

    public function getInput(): TokenizedInputInterface
    {
        return $this->input;
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