<?php

namespace WorldFactory\QQ\Services\Runners;

use function array_shift;
use function explode;
use Exception;
use WorldFactory\QQ\Misc\BasicCommand;
use WorldFactory\QQ\Misc\ExtendedArgvInput;

class CommandRunner extends AbstractRunner
{
    /**
     * @param string $script
     * @throws \Exception
     */
    public function run(string $script) : void
    {
        $explodedScript = explode(' ', $script);
        $commandName = $explodedScript[0];

        $command = $this->getApplication()->find($commandName);

        if ($command instanceof BasicCommand) {
            $command->setDisplayHeader(false);
        }

        $this->getOutput()->writeln("Running sub-command...");

        $returnCode = $command->run(new ExtendedArgvInput(explode(' ', $script)), $this->getOutput());

        if ($returnCode !== 0) {
            throw new Exception("An error occur when running command : $script");
        }
    }
}