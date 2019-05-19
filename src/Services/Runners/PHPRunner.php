<?php

namespace WorldFactory\QQ\Services\Runners;

use Exception;
use WorldFactory\QQ\Foundations\AbstractRunner;
use WorldFactory\QQ\Misc\TemporizedExecution;

class PHPRunner extends AbstractRunner
{
    protected const SHORT_DESCRIPTION = "Execute PHP code with 'eval' function.";

    protected const LONG_DESCRIPTION = <<<EOT
This Runner allows you to execute PHP code with the 'eval' function.
The context is the 'run' method of the PHPRunner class.
You have at your disposal a \$script object of type WorldFactory\QQ\Entities\Script, as well as all the protected and private methods of the PHPRunner.
EOT;

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function execute(string $script)
    {
        $execution = new TemporizedExecution($this->getOutput(), function() use ($script) {
            eval($script);
        });

        $execution->execute();

        $this->getOutput()->writeln('');

        return $execution->getBuffer()->get();
    }
}