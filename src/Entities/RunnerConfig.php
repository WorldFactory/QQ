<?php

namespace WorldFactory\QQ\Entities;

use WorldFactory\QQ\Interfaces\RunnerInterface;
use WorldFactory\QQ\Interfaces\ScriptFormatterInterface;
use WorldFactory\QQ\Misc\OptionBag;

class RunnerConfig extends OptionBag
{
    /** @var array  */
    private $compiledOptions = [];

    public function __construct(array $options = [])
    {
        parent::__construct($options);

        $this->addOptionDefinitions([
            'type'     => [
                'type' => 'string',
                'required' => true,
                'description' => "The default type to define which runner to be used."
            ]
        ]);
    }

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