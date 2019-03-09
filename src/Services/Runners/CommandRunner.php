<?php

namespace WorldFactory\QQ\Services\Runners;

use function array_shift;
use function explode;
use Exception;
use Symfony\Component\Console\Input\StringInput;
use WorldFactory\QQ\Application;
use WorldFactory\QQ\Entities\Script;
use WorldFactory\QQ\Foundations\AbstractRunner;
use WorldFactory\QQ\Misc\BasicCommand;
use WorldFactory\QQ\Misc\Inputs\StringTokenizedInput;

class CommandRunner extends AbstractRunner
{
    protected const SHORT_DESCRIPTION = "Run QQ sub-command.";

    protected const LONG_DESCRIPTION = <<<EOT
The command is executed directly in the current execution context.
Feel free to compose your scripts with other subcommands to factorize your 'commands.yml' file.
EOT;

    /** @var Application */
    private $application;

    /**
     * @param Application $application
     */
    public function setApplication(Application $application): void
    {
        $this->application = $application;
    }

    /**
     * @param Script $script
     * @throws \Exception
     */
    public function run(Script $script) : void
    {
        $arguments = explode(' ', $script->getCompiledScript());
        $commandName = array_shift($arguments);

        $command = $this->application->find($commandName);

        if ($command instanceof BasicCommand) {
            $command->setDisplayHeader(false);

            $input = new StringTokenizedInput($script->getCompiledScript());
        } else {
            $input = new StringInput($script->getCompiledScript());
        }

        $this->getOutput()->writeln("Running sub-command...");

        $returnCode = $command->run($input, $this->getOutput());

        if ($returnCode !== 0) {
            throw new Exception("An error occur when running command : {$script->getCompiledScript()}");
        }
    }
}