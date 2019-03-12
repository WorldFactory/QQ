<?php

namespace WorldFactory\QQ\Misc;

use WorldFactory\QQ\Interfaces\RunnerInterface;
use WorldFactory\QQ\Interfaces\ScriptFormatterInterface;

class RunnerOptionBag extends OptionBag
{
    /** @var array  */
    private $compiledOptions = [];

    public function link(RunnerInterface $runner)
    {
        $this->addOptionDefinitions($runner->getOptionDefinitions());

        $runner->setOptions($this);
    }

    protected function getOption(string $name)
    {
        $value = array_key_exists($name, $this->compiledOptions) ?
            $this->compiledOptions[$name] :
            parent::getOption($name);

        return $value;
    }

    protected function has(string $name)
    {
        return (array_key_exists($name, $this->compiledOptions) || parent::has($name));
    }

    /**
     * @param ScriptFormatterInterface $formatter
     */
    public function compile(ScriptFormatterInterface $formatter)
    {
        $options = array_merge($this->getDefaultOptions(), $this->getOptions());

        foreach ($options as $name => $option) {
            $this->compiledOptions[$name] = is_string($option) ? $formatter->format($option) : $option;
        }
    }
}