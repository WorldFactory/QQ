<?php

namespace WorldFactory\QQ\Services\Runners;

class NullRunner extends AbstractRunner
{
    /**
     * Do nothing !!
     * @param string $script
     */
    public function run(string $script) : void
    {
    }
}