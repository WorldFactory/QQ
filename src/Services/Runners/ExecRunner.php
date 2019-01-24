<?php

namespace WorldFactory\QQ\Services\Runners;

use WorldFactory\QQ\Entities\Script;

class ExecRunner extends AbstractRunner
{
    /**
     * @param Script $script
     * @throws \Exception
     */
    public function run(Script $script) : void
    {
        passthru($script->getCompiledScript());
    }
}