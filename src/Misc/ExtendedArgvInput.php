<?php

namespace WorldFactory\QQ\Misc;

use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputDefinition;

class ExtendedArgvInput extends ArgvInput
{
    private $savedTokens;

    /**
     * @param array|null           $argv       An array of parameters from the CLI (in the argv format)
     * @param InputDefinition|null $definition A InputDefinition instance
     */
    public function __construct(array $argv = null, InputDefinition $definition = null)
    {
        if (null === $argv) {
            $argv = $_SERVER['argv'];
            $this->savedTokens = array_slice($argv, 1);
        } else {
            $this->savedTokens = $argv;
        }

        parent::__construct($argv, $definition);
    }

    public function getSavedTokens()
    {
        return $this->savedTokens;
    }
}