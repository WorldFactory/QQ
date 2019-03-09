<?php

namespace WorldFactory\QQ\Entities;

use ArrayAccess;

class Context implements ArrayAccess
{
    private $parameters = [];

    private $input;

    private $output;

    private $tokens = [];

    public function __construct(array $parameters, array $tokens, $input, $output)
    {
        $this->parameters = $parameters;
        $this->tokens = $tokens;
        $this->input = $input;
        $this->output = $output;
    }

    /**
     * @return mixed
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * @return mixed
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @return array
     */
    public function getTokens(): array
    {
        return $this->tokens;
    }

    public function get(string $name)
    {
        return $this->has($name) ? $this->parameters[$name] : null;
    }

    public function set(string $name, $value)
    {
        $this->parameters[$name] = $value;
    }

    public function unset(string $name)
    {
        if ($this->has($name)) {
            unset($this->parameters[$name]);
        }
    }

    public function has(string $name) : bool
    {
        return array_key_exists($name, $this->parameters);
    }

    /**
     * @param string $offset
     * @return mixed|null
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @param string $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value) : void
    {
        $this->set($offset, $value);
    }

    /**
     * @param string $offset
     */
    public function offsetUnset($offset) : void
    {
        $this->unset($offset);
    }

    /**
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset) : bool
    {
        return $this->has($offset);
    }
}