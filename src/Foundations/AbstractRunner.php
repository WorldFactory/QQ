<?php

namespace WorldFactory\QQ\Foundations;

use WorldFactory\QQ\Components\Context;
use WorldFactory\QQ\Interfaces\TokenizedInputInterface;
use WorldFactory\QQ\Interfaces\RunnerInterface;
use WorldFactory\QQ\Misc\RunnerOptionBag;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractRunner implements RunnerInterface
{
    protected const OPTION_DEFINITIONS = [];
    protected const SHORT_DESCRIPTION = "No short description provided.";
    protected const LONG_DESCRIPTION = "No long description provided.";

    /** @var RunnerOptionBag */
    private $options;

    /** @var OutputInterface */
    private $output;

    /** @var TokenizedInputInterface */
    private $input;

    /** @var Context */
    private $context;

    /** @var bool */
    private $headerDisplayed = true;

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

    public function run(string $script, Context $context)
    {
        $this->context = $context;

        if ($this->getOutput()->isVerbose()) {
            $class = get_class($this);
            $this->getOutput()->writeln("-> Runner : <fg=magenta>{$class}</>");
        }

        if ($this->isHeaderDisplayed()) {
            $this->getOutput()->writeln("-> <fg=black;bg=green>{$script}</>");
        }

        return $this->execute($script);
    }

    abstract public function execute(string $script);

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

    public function setOptions(RunnerOptionBag $options)
    {
        $this->options = $options;
    }

    /**
     * @return RunnerOptionBag
     */
    protected function getOptions() : RunnerOptionBag
    {
        return $this->options;
    }

    protected function getOption(string $name)
    {
        return $this->options[$name];
    }

    protected function hasOption(string $name) : bool
    {
        return isset($this->options[$name]);
    }

    protected function isHeaderDisplayed() : bool
    {
        return $this->headerDisplayed;
    }

    /**
     * @inheritdoc
     */
    public function setHeaderDisplayed(bool $headerDisplayed) : RunnerInterface
    {
        $this->headerDisplayed = $headerDisplayed;

        return $this;
    }

    /**
     * @return Context
     */
    protected function getContext(): Context
    {
        return $this->context;
    }
}