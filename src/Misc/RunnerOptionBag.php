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
        $this->compiledOptions = $this->compileArray(
            $formatter,
            array_merge($this->getDefaultOptions(), $this->getOptions())
        );
    }

    /**
     * @param ScriptFormatterInterface $formatter
     * @param array $data
     * @return array
     */
    protected function compileArray(ScriptFormatterInterface $formatter, array $data)
    {
        $compiledData = [];

        foreach ($data as $key => $val) {
            if (is_string($val)) {
                $val = $formatter->format($val);
            } elseif (is_array($val)) {
                $val = $this->compileArray($formatter, $val);
            }

            $compiledData[$key] = $val;
        }

        return $compiledData;
    }
}