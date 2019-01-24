<?php

namespace WorldFactory\QQ\Services\Runners;

use WorldFactory\QQ\Entities\Script;

class IncludeRunner extends AbstractRunner
{
    /**
     * @param Script $script
     * @throws \Exception
     */
    public function run(Script $script) : void
    {
        require($script->getCompiledScript());
    }
}