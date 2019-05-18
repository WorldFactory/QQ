<?php

namespace WorldFactory\QQ\Services\Runners;

use Exception;
use DateTime;
use WorldFactory\QQ\Foundations\AbstractRunner;
use WorldFactory\QQ\Misc\TemporizedExecution;

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
     * @throws Exception
     */
    public function execute(string $script)
    {
        $filename = $this->getFilename();

        $this->writeScript($script, $filename);

        $execution = new TemporizedExecution($this->getOutput(), function() use ($filename) {
            chmod($filename, 0755);
            passthru($filename);
        });

        $execution->setFinallyHook(function() use ($filename) {
            unlink($filename);
        });

        $execution->execute();

        return $execution->getBuffer()->get();
    }

    protected function getFilename() : string
    {
        $varDir = getcwd() . '/var/tmp';

        if (!is_dir($varDir)) {
            mkdir($varDir, 0777, true);
        }

        $hash = sha1((DateTime::createFromFormat('U.u', microtime(TRUE)))->format('Y-m-d H:i:s:u'));

        return "$varDir/$hash.sh";
    }

    protected function writeScript(string $script, string $filename) : void
    {
        $extendedScript = <<<EOT
#!/usr/bin/env bash

{$script}

EOT;

        if ($this->getOption('pause')) {
            $extendedScript .= <<<EOT
read -n1 -r -p "Press any key to continue..." key

EOT;
        }


        file_put_contents($filename, $extendedScript);
    }
}