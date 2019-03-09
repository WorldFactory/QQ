<?php

namespace WorldFactory\QQ\Foundations;

use WorldFactory\QQ\Interfaces\ScriptFormatterInterface;
use WorldFactory\QQ\Interfaces\TokenizedInputInterface;
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

    /** @var ConfigLoader */
    private $configLoader;

    /** @var ScriptFormatterInterface */
    private $varFormatter;

    public function __construct(ConfigLoader $configLoader)
    {
        $this->configLoader = $configLoader;
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

    public function format(string $compiledScript) : string
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

    public function run(string $script): void
    {
        if ($this->getOutput()->isVerbose()) {
            $class = get_class($this);
            $this->getOutput()->writeln("-> Runner : <fg=magenta>{$class}</>");
        }

        if ($this->isHeaderDisplayed()) {
            $this->getOutput()->writeln("-> <fg=black;bg=green>{$script}</>");
        }

        $this->execute($script);
    }

    abstract public function execute(string $script) : void;

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

    public function isHeaderDisplayed() : bool
    {
        return true;
    }
}