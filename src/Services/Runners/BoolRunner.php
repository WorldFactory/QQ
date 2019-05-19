<?php

namespace WorldFactory\QQ\Services\Runners;

use Exception;
use WorldFactory\QQ\Foundations\AbstractRunner;
use WorldFactory\QQ\Misc\TemporizedExecution;

class BoolRunner extends AbstractRunner
{
    protected const SHORT_DESCRIPTION = "Run script in CLI with 'system' PHP function.";

    protected const LONG_DESCRIPTION = <<<EOT
Run script using 'system' PHP function.
The result of the command will depend on the error code returned by the script.
If an error is detected, it will not be blocking and will only give rise to an error message.
EOT;

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function execute(string $script)
    {
        $execution = new TemporizedExecution($this->getOutput(), function() use ($script) {
            system($script, $returnCode);

            if ($returnCode) {
                throw new Exception("Unknown system error : '$returnCode' for command : $script");
            }
        });

        $result = true;

        try {
            $execution->execute();
        } catch (Exception $exception) {
            $result = false;

            $this->getOutput()->writeln("<error>Catched error : {$exception->getMessage()}</error>");
        }

        return $result;
    }
}