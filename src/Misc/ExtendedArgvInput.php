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
        }

//        $this->savedTokens = array_slice($argv, 1);
        $this->savedTokens = $argv;
        array_shift($this->savedTokens);

        parent::__construct($argv, $definition);
    }

    protected function setTokens(array $tokens)
    {
        $this->savedTokens = $tokens;

        parent::setTokens($tokens);
    }

    public function getSavedTokens()
    {
        return $this->savedTokens;
    }
}