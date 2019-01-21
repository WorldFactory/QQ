<?php

namespace WorldFactory\QQ\Services\Runners;

use function array_shift;
use function explode;
use Exception;
use Symfony\Component\Console\Input\StringInput;
use WorldFactory\QQ\Misc\BasicCommand;
use WorldFactory\QQ\Misc\Inputs\StringTokenizedInput;

class CommandRunner extends AbstractRunner
{
    /**
     * @param string $script
     * @throws \Exception
     */
    public function run(string $script) : void
    {
        $arguments = explode(' ', $script);
        $commandName = array_shift($arguments);

        $command = $this->getApplication()->find($commandName);

        if ($command instanceof BasicCommand) {
            $command->setDisplayHeader(false);

            $input = new StringTokenizedInput($script);
        } else {
            $input = new StringInput($script);
        }

        $this->getOutput()->writeln("Running sub-command...");

        $returnCode = $command->run($input, $this->getOutput());

        if ($returnCode !== 0) {
            throw new Exception("An error occur when running command : $script");
        }
    }
}