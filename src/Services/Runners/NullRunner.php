<?php

namespace WorldFactory\QQ\Services\Runners;

use WorldFactory\QQ\Entities\Script;

class NullRunner extends AbstractRunner
{
    /**
     * Do nothing !!
     * @param Script $script
     */
    public function run(Script $script) : void
    {
    }
}