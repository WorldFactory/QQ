<?php

namespace WorldFactory\QQ\Interfaces;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StreamableInputInterface;

interface TokenizedInputInterface extends InputInterface, StreamableInputInterface
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
