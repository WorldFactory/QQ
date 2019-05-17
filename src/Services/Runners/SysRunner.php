<?php

namespace WorldFactory\QQ\Services\Runners;

use Exception;
use WorldFactory\QQ\Foundations\AbstractRunner;
use WorldFactory\QQ\Misc\TemporizedExecution;

class SysRunner extends AbstractRunner
{
    protected const SHORT_DESCRIPTION = "Run script in CLI with 'system' PHP function.";

    protected const LONG_DESCRIPTION = <<<EOT
Run script using 'system' PHP function.
Historically, this Runner was created to overcome some cases where the Runner Shell was struggling to function.
In the meantime, the Runner Shell has been greatly improved and the cases where the Runner Exec is useful have become very rare.
This Runner has still been preserved to provide an alternative in case of trouble.
It is always recommended to use the ShellRunner to execute a system command. This runner exists to mitigate any special cases.
EOT;

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function execute(string $script) : void
    {
        $execution = new TemporizedExecution($this->getBuffer(), $this->getOutput(), function() use ($script) {
            system($script, $returnCode);

            if ($returnCode) {
                throw new Exception("Unknown system error : '$returnCode' for command : $script");
            }
        });

        $execution->execute();
    }
}