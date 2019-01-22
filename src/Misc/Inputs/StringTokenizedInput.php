<?php

namespace WorldFactory\QQ\Misc\Inputs;

use Symfony\Component\Console\Input\StringInput;
use WorldFactory\QQ\Interfaces\TokenizedInputInterface;

class StringTokenizedInput extends StringInput implements TokenizedInputInterface
{
    use TokenizedInputTrait;

    /**
     * Not used. Waiting for hotfix merge request.
     * @TODO Create merge request.
     * @param array $tokens
     */
    protected function setTokens(array $tokens)
    {
        $this->setArgumentTokens(array_slice($tokens, 1));

        parent::setTokens($tokens);
    }
}