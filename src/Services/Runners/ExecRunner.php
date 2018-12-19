<?php

namespace WorldFactory\QQ\Services\Runners;

class ExecRunner extends AbstractRunner
{
    /**
     * @param string $script
     * @throws \Exception
     */
    public function run(string $script)
    {
        passthru($script);
    }
}