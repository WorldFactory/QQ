<?php

namespace WorldFactory\QQ\Services\Runners;

use WorldFactory\QQ\Entities\Script;

class PhpRunner extends AbstractRunner
{
    /**
     * @param Script $script
     */
    public function run(Script $script) : void
    {
        eval($script->getCompiledScript());

        $this->getOutput()->writeln('');
    }
}