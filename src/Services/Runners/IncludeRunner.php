<?php

namespace WorldFactory\QQ\Services\Runners;

class IncludeRunner extends AbstractRunner
{
    /**
     * @param string $script
     * @throws \Exception
     */
    public function run(string $script)
    {
        require($script);
    }
}