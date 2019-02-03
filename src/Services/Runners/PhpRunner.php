<?php

namespace WorldFactory\QQ\Services\Runners;

use WorldFactory\QQ\Entities\Script;

class PhpRunner extends AbstractRunner
{
    protected const SHORT_DESCRIPTION = "Execute PHP code with 'eval' function.";

    /**
     * @param Script $script
     */
    public function run(Script $script) : void
    {
        eval($script->getCompiledScript());

        $this->getOutput()->writeln('');
    }
}