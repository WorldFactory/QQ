<?php

namespace WorldFactory\QQ\Services;

use DateTime;
use WorldFactory\QQ\Components\Hosts;

class HostsHandler
{
    public function saveHosts(Hosts $hosts)
    {
        $target = $hosts->getTarget();
        $filename = $this->getFilename();

        file_put_contents($filename, $hosts->buildContent());

        system("sudo cp -f $filename $target && rm $filename", $result);

        return ($result === 0);
    }

    protected function getFilename() : string
    {
        $varDir = getcwd() . '/var/tmp';

        if (!is_dir($varDir)) {
            mkdir($varDir, 0777, true);
        }

        $hash = sha1((DateTime::createFromFormat('U.u', microtime(TRUE)))->format('Y-m-d H:i:s:u'));

        return "$varDir/$hash";
    }
}