<?php

namespace WorldFactory\QQ\Interfaces;

use Symfony\Component\Console\Input\InputInterface;

interface TokenizedInputInterface extends InputInterface
{
    /**
     * @param array $tokens
     */
    public function setArgumentTokens(array $tokens) : void;

    /**
     * @return array
     */
    public function getArgumentTokens() : array;
}
