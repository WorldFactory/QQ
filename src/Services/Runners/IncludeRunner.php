<?php

namespace WorldFactory\QQ\Services\Runners;

use Exception;
use WorldFactory\QQ\Foundations\AbstractRunner;
use WorldFactory\QQ\Misc\TemporizedExecution;

class IncludeRunner extends AbstractRunner
{
    protected const SHORT_DESCRIPTION = "Include target PHP file.";

    protected const LONG_DESCRIPTION = <<<EOT
The script to run must be a valid PHP file.
This is included with the 'require' function.
The context is the 'run' method of the IncludeRunner class.
You have at your disposal a \$script object of type WorldFactory\QQ\Entities\Script, as well as all the protected and private methods of the IncludeRunner.
EOT;

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function execute(string $script) : void
    {
        $execution = new TemporizedExecution($this->getBuffer(), $this->getOutput(), function() use ($script) {
            require($script);
        });

        $execution->execute();
    }
}