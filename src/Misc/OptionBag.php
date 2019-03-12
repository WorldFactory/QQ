<?php

namespace WorldFactory\QQ\Misc;

use ArrayAccess;
use InvalidArgumentException;

class OptionBag implements ArrayAccess
{
    /** @var array */
    private $options = [];

    /** @var array */
    private $defaultOptions = [];

    private $optionDefinitions = [
        'type'     => [
            'type' => 'string',
            'required' => true,
            'description' => "The default type to define which runner to be used."
        ]
    ];

    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    /**
     * @param array $optionDefinitions
     */
    public function addOptionDefinitions(array $optionDefinitions): void
    {
        $this->optionDefinitions = array_merge($this->optionDefinitions, $optionDefinitions);

        foreach($optionDefinitions as $option => $definition) {
            if (isset($definition['default'])) {
                $this->defaultOptions[$option] = $definition['default'];
            }
        }
    }

    protected function get(string $name)
    {
        $value = $this->getOption($name);

        $this->verifyOption($name, $value);

        return $value;
    }

    protected function getOption(string $name)
    {
        $value = array_key_exists($name, $this->options) ?
            $this->options[$name] :
            (array_key_exists($name, $this->defaultOptions) ?
                $this->defaultOptions[$name] :
                null);

        return $value;
    }

    /**
     * @return array
     */
    protected function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @return array
     */
    protected function getDefaultOptions(): array
    {
        return $this->defaultOptions;
    }

    protected function verifyOption($name, $value)
    {
        if (array_key_exists($name, $this->optionDefinitions)) {
            if (in_array($value, [null, false, '', []]) && $this->isRequired($name)) {
                throw new InvalidArgumentException("The '$name' option must be set.");
            }

            if (array_key_exists('type', $this->optionDefinitions[$name])) {
                switch ($this->optionDefinitions[$name]['type']) {
                    case 'array':
                        if (!is_array($value)) {
                            throw new InvalidArgumentException("The '$name' option must be an array.");
                        }
                        break;
                    case 'string':
                        if (!is_string($value)) {
                            throw new InvalidArgumentException("The '$name' option must be a string.");
                        }
                        break;
                    case 'int':
                    case 'integer':
                        if (!is_int($value)) {
                            throw new InvalidArgumentException("The '$name' option must be an integer.");
                        }
                        break;
                    case 'float':
                        if (!is_float($value)) {
                            throw new InvalidArgumentException("The '$name' option must be a float.");
                        }
                        break;
                    case 'numeric':
                        if (!is_numeric($value)) {
                            throw new InvalidArgumentException("The '$name' option must be numeric.");
                        }
                        break;
                    case 'bool':
                    case 'boolean':
                        if (!is_bool($value)) {
                            throw new InvalidArgumentException("The '$name' option must be a boolean.");
                        }
                        break;
                    default:
                        throw new InvalidArgumentException("The '$name' option has an invalid type : '{$this->optionDefinitions[$name]['type']}'.");
                }
            }
        }
    }

    protected function has(string $name)
    {
        return (array_key_exists($name, $this->options) || array_key_exists($name, $this->defaultOptions));
    }

    protected function isRequired(string $name)
    {
        return (
            array_key_exists($name, $this->optionDefinitions) &&
            array_key_exists('required', $this->optionDefinitions[$name]) &&
            ($this->optionDefinitions[$name]['required'] === true)
        );
    }

    public function merge(array $options)
    {
        return new static(array_merge($this->options, $options));
    }

    public function clone()
    {
        return new static($this->options);
    }

    public function toArray()
    {
        return $this->options;
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