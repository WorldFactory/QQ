<?php

namespace WorldFactory\QQ\Entities;

use ArrayAccess;
use WorldFactory\QQ\Interfaces\ScriptFormatterInterface;

class ScriptConfig implements ArrayAccess
{
    /** @var array  */
    private $compiledOptions = [];

    /** @var array */
    private $options = [];

    /** @var array */
    private $applicationOptions = [];

    /** @var array */
    private $defaultOptions = [];

    public function __construct(array $options, array $applicationOptions = [])
    {
        $this->options = $options;
        $this->applicationOptions = $applicationOptions;
    }

    /**
     * @param array $defaultOptions
     */
    public function setDefaultOptions(array $defaultOptions) : void
    {
        $this->defaultOptions = $defaultOptions;
    }

    protected function get(string $name)
    {
        return array_key_exists($name, $this->compiledOptions) ?
            $this->compiledOptions[$name] :
            array_key_exists($name, $this->options) ?
                $this->options[$name] :
                array_key_exists($name, $this->applicationOptions) ?
                    $this->applicationOptions[$name] :
                    array_key_exists($name, $this->defaultOptions) ?
                        $this->defaultOptions[$name] :
                        null;
    }

    protected function has(string $name)
    {
        return (array_key_exists($name, $this->compiledOptions) || array_key_exists($name, $this->options) || array_key_exists($name, $this->applicationOptions) || array_key_exists($name, $this->defaultOptions));
    }

    public function merge(array $options)
    {
        return new ScriptConfig(array_merge($this->options, $options), $this->applicationOptions);
    }

    public function clone()
    {
        return new ScriptConfig($this->options, $this->applicationOptions);
    }

    /**
     * @param ScriptFormatterInterface $formatter
     */
    public function compile(ScriptFormatterInterface $formatter)
    {
        $options = array_merge($this->defaultOptions, $this->applicationOptions, $this->options);

        foreach ($options as $name => $option) {
            $this->compiledOptions[$name] = $formatter->format($option);
        }
    }

    /**
     * @param string $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value) : void
    {
        throw new \LogicException("Script configuration is read only.");
    }

    /**
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset) : bool
    {
        return $this->has($offset);
    }

    /**
     * @param string $offset
     */
    public function offsetUnset($offset) : void
    {
        throw new \LogicException("Script configuration is read only.");
    }

    /**
     * @param string $offset
     * @return mixed|null
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }
}