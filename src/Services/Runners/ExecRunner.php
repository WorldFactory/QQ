<?php

namespace WorldFactory\QQ\Services\Runners;

use WorldFactory\QQ\Entities\Script;

class ExecRunner extends AbstractRunner
{
    protected const SHORT_DESCRIPTION = "Run script in CLI with 'passthru' PHP function.";

    /**
     * @param Script $script
     * @throws \Exception
     */
    public function run(Script $script) : void
    {
        passthru($script->getCompiledScript());
    }
}