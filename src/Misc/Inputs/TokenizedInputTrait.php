<?php

namespace WorldFactory\QQ\Misc\Inputs;


trait TokenizedInputTrait
{
    /** @var array */
    private $argumentTokens = [];

    /**
     * @return array
     */
    public function getArgumentTokens() : array
    {
        return $this->argumentTokens;
    }

    public function setArgumentTokens(array $tokens) : void
    {
        $this->argumentTokens = $tokens;
    }
}