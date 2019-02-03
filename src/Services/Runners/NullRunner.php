<?php

namespace WorldFactory\QQ\Services\Runners;

use WorldFactory\QQ\Entities\Script;

class NullRunner extends AbstractRunner
{
    protected const SHORT_DESCRIPTION = "Do nothing. Displays only the script.";

    protected const LONG_DESCRIPTION = <<<EOT
Do nothing !!
This Runner is a kind of black hole that does nothing at all.
It can be used to comment one or more lines inside a multi-line script.
EOT;

    /**
     * Do nothing !!
     * @param Script $script
     */
    public function run(Script $script) : void
    {
    }

    public function isHeaderDisplayed() : bool
    {
        return false;
    }
}