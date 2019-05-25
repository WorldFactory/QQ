<?php

namespace WorldFactory\QQ\Services\Runners;

use Exception;
use WorldFactory\QQ\Foundations\AbstractRunner;

class ArrayRunner extends AbstractRunner
{
    protected const SHORT_DESCRIPTION = "Run script in CLI with 'exec' PHP function and return Array of lines.";

    protected const LONG_DESCRIPTION = <<<EOT
Run script using 'exec' PHP function.
This Runner is different from other PHP-based Runner system because it does not allow a progressive display of the return of the executed command.
In other words, you will only get a display once the command is complete.
It is not suitable for long process.
On the other hand, the output is cleaned and can more easily be recovered for subsequent treatments.
The result of this command is also in the form of an array of cleaned lines
EOT;

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function execute(string $script)
    {
        exec($script, $output, $returnCode);

        $this->getOutput()->writeln($output);

        if ($returnCode) {
            throw new Exception("Unknown system error : '$returnCode' for command : $script");
        }

        return $output;
    }
}