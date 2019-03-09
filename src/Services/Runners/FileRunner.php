<?php

namespace WorldFactory\QQ\Services\Runners;

use DateTime;
use WorldFactory\QQ\Foundations\AbstractRunner;

class FileRunner extends AbstractRunner
{
    const OPTION_DEFINITIONS = [
        'pause' => [
            'type' => 'bool',
            'default' => false,
            'description' => "If set to True, display a message before continuing."
        ]
    ];

    protected const SHORT_DESCRIPTION = "Save script in file and run it.";

    protected const LONG_DESCRIPTION = <<<EOT
The file is saved in the ./var/tmp directory and executed with the PHP passthru command.
EOT;

    /**
     * @inheritdoc
     */
    public function execute(string $script) : void
    {
        $varDir = getcwd() . '/var/tmp';

        if (!is_dir($varDir)) {
            mkdir($varDir);
        }

        $hash = sha1((DateTime::createFromFormat('U.u', microtime(TRUE)))->format('Y-m-d H:i:s:u'));

        $tmpScriptName = $varDir . '/' . $hash . '.sh';

        $extendedScript = <<<EOT
#!/usr/bin/env bash

{$script}

EOT;

        if ($this->getOption('pause')) {
            $extendedScript .= <<<EOT
read -n1 -r -p "Press any key to continue..." key

EOT;
        }


        file_put_contents($tmpScriptName, $extendedScript);

        chmod($tmpScriptName, 0755);

        passthru($tmpScriptName);

        unlink($tmpScriptName);
    }
}