<?php

namespace WorldFactory\QQ\Misc\Inputs;

use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputDefinition;
use WorldFactory\QQ\Interfaces\TokenizedInputInterface;

class ArgvTokenizedInput extends ArgvInput implements TokenizedInputInterface
{
    use TokenizedInputTrait;

    /**
     * @param array|null           $argv       An array of parameters from the CLI (in the argv format)
     * @param InputDefinition|null $definition A InputDefinition instance
     */
    public function __construct(array $argv = null)
    {
        if (null === $argv) {
            $argv = $_SERVER['argv'];
        }

        parent::__construct($argv);

        $firstArg = $this->getFirstArgument();

        $this->setArgumentTokens(array_slice($argv, 1 + array_search($firstArg, $argv)));
    }

    /**
     * Not used. Waiting for hotfix merge request.
     * @TODO Create merge request.
     * @param array $tokens
     */
    protected function setTokens(array $tokens)
    {
        $this->setArgumentTokens($tokens);

        parent::setTokens($tokens);
    }
}