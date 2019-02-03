<?php

namespace WorldFactory\QQ\Services\Runners;

use WorldFactory\QQ\Entities\Script;

class NullRunner extends AbstractRunner
{
    protected const SHORT_DESCRIPTION = "Do nothing. Displays only the script.";

    /**
     * Do nothing !!
     * @param Script $script
     */
    public function run(Script $script) : void
    {
    }
}