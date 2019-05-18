<?php

namespace WorldFactory\QQ\Services\Runners;

use Exception;
use WorldFactory\QQ\Foundations\AbstractRunner;
use WorldFactory\QQ\Misc\TemporizedExecution;

class PassRunner extends AbstractRunner
{
    protected const SHORT_DESCRIPTION = "Run script in CLI with 'passthru' PHP function.";

    protected const LONG_DESCRIPTION = <<<EOT
Run script using 'passthru' PHP function.
This Runner is configured to delay the entire output of the executed command. This is to preserve its content. This behavior is more suitable in the case of a binary output.
It is always recommended to use the ShellRunner to execute a system command. This runner exists to mitigate any special cases.
EOT;

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function execute(string $script)
    {
        $execution = new TemporizedExecution($this->getOutput(), function() use ($script) {
            passthru($script, $returnCode);

            if ($returnCode) {
                throw new Exception("Unknown system error : '$returnCode' for command : $script");
            }
        });

        $execution->setChunkSize(0);

        $execution->execute();

        return $execution->getBuffer()->get();
    }
}