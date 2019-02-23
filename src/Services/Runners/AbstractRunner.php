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
    protected const OPTION_DEFINITIONS = [];
    protected const SHORT_DESCRIPTION = "No short description provided.";
    protected const LONG_DESCRIPTION = "No long description provided.";

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

    public function getOptionDefinitions() : array
    {
        return static::OPTION_DEFINITIONS;
    }

    public function getShortDescription() : string
    {
        return static::SHORT_DESCRIPTION;
    }

    public function getLongDescription() : string
    {
        return static::LONG_DESCRIPTION;
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

    /**
     * @return Application
     * @deprecated 1.5.0
     */
    public function getApplication() : Application
    {
        return $this->application;
    }

    /**
     * @param BasicCommand $command
     * @param bool $hide
     * @return RunnerInterface
     * @deprecated 1.5.0
     */
    public function setCommand(BasicCommand $command, bool $hide = false) : RunnerInterface
    {
        if (!$hide) {
            trigger_error("Method 'setCommand' is deprecated.", E_USER_DEPRECATED);
        }

        $this->command = $command;

        return $this;
    }

    /**
     * @param bool $hide
     * @return BasicCommand
     * @deprecated 1.5.0
     */
    public function getCommand(bool $hide = false) : BasicCommand
    {
        if (!$hide) {
            trigger_error("Method 'getCommand' is deprecated.", E_USER_DEPRECATED);
        }

        return $this->command;
    }

    public function isHeaderDisplayed() : bool
    {
        return true;
    }
}