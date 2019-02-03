<?php

namespace WorldFactory\QQ\Services\Runners;

use WorldFactory\QQ\Entities\Script;

class ChildRunner extends AbstractRunner
{
    const OPTION_DEFINITIONS = [
        'pause' => ['type' => 'bool', 'default' => false]
    ];

    protected const SHORT_DESCRIPTION = "Save script in file and run it.";

    protected const LONG_DESCRIPTION = <<<EOT
The file is saved in the ./var/tmp directory and executed with the PHP passthru command.
EOT;

    /**
     * @param Script $script
     * @throws \Exception
     */
    public function run(Script $script) : void
    {
        $varDir = getcwd() . '/var/tmp';

        if (!is_dir($varDir)) {
            mkdir($varDir);
        }

        $tmpScriptName = $varDir . '/' . str_replace(':', '-', $this->getCommand()->getName()) . '.sh';

        $extendedScript = <<<EOT
#!/usr/bin/env bash

{$script->getCompiledScript()}

EOT;

        if ($script->getOption('pause')) {
            $extendedScript .= <<<EOT
read -n1 -r -p "Press any key to continue..." key

EOT;
        }


        file_put_contents($tmpScriptName, $extendedScript);

        chmod($tmpScriptName, 0755);

        passthru($tmpScriptName);
    }
}