<?php

namespace WorldFactory\QQ\Services\Runners;

use function array_shift;
use function explode;
use Exception;
use Symfony\Component\Console\Input\StringInput;
use WorldFactory\QQ\Application;
use WorldFactory\QQ\Foundations\AbstractRunner;
use WorldFactory\QQ\Misc\BasicCommand;
use WorldFactory\QQ\Misc\Inputs\StringTokenizedInput;
use WorldFactory\QQ\Misc\Outputs\ReplicatedOutput;

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
     * @inheritdoc
     * @throws Exception
     */
    public function execute(string $script) : void
    {
        $arguments = explode(' ', $script);
        $commandName = array_shift($arguments);

        $command = $this->application->find($commandName);

        if ($command instanceof BasicCommand) {
            $command->setDisplayHeader(false);

            $input = new StringTokenizedInput($script);
        } else {
            $input = new StringInput($script);
        }

        $this->getOutput()->writeln("Running sub-command...");

        $returnCode = $command->run($input, $this->getReplicatedOutput());

        if ($returnCode !== 0) {
            throw new Exception("Unknown system error : '$returnCode' for command :  {$script}");
        }
    }

    protected function getReplicatedOutput()
    {
        $output = $this->getOutput();

        if ($output instanceof ReplicatedOutput) {
            $output = $output->getOriginalOutput();
        }

        return new ReplicatedOutput($output, $this->getBuffer());
    }
}