<?php

namespace WorldFactory\QQ\Components;

use ArrayAccess;
use Symfony\Component\Console\Output\OutputInterface;
use WorldFactory\QQ\Interfaces\TokenizedInputInterface;
use WorldFactory\QQ\Misc\ContextualizedFormatter;

class Context implements ArrayAccess
{
    private $parameters = [];

    /** @var TokenizedInputInterface */
    private $input;

    /** @var OutputInterface */
    private $output;

    private $tokens = [];

    public function __construct(array $parameters, array $tokens, TokenizedInputInterface $input, OutputInterface $output)
    {
        $formatter = new ContextualizedFormatter($this);

        $this->parameters = $formatter->injectEnvVarsRecursively($parameters);
        $this->tokens = $tokens;
        $this->input = $input;
        $this->output = $output;
    }

    /**
     * @return TokenizedInputInterface
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * @return OutputInterface
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