<?php

namespace WorldFactory\QQ\Services\Runners;

class ChildRunner extends AbstractRunner
{
    /**
     * @param string $script
     * @throws \Exception
     */
    public function run(string $script)
    {
        $varDir = getcwd() . '/var/tmp';

        if (!is_dir($varDir)) {
            mkdir($varDir);
        }

        $tmpScriptName = $varDir . '/' . str_replace(':', '-', $this->getCommand()->getName()) . '.sh';

        $extendedScript = <<<EOT
#!/usr/bin/env bash

$script

read -n1 -r -p "Press any key to continue..." key
EOT;


        file_put_contents($tmpScriptName, $extendedScript);

        chmod($tmpScriptName, 0766);

        passthru($tmpScriptName);
    }
}