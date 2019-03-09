<?php

namespace WorldFactory\QQ\Services\Runners;

use WorldFactory\QQ\Foundations\AbstractRunner;

class PhpRunner extends AbstractRunner
{
    protected const SHORT_DESCRIPTION = "Execute PHP code with 'eval' function.";

    protected const LONG_DESCRIPTION = <<<EOT
This Runner allows you to execute PHP code with the 'eval' function.
The context is the 'run' method of the PhpRunner class.
You have at your disposal a \$script object of type WorldFactory\QQ\Entities\Script, as well as all the protected and private methods of the PhpRunner.
EOT;

    /**
     * @inheritdoc
     */
    public function execute(string $script) : void
    {
        eval($script);

        $this->getOutput()->writeln('');
    }
}