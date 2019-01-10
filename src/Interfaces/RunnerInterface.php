<?php

namespace WorldFactory\QQ\Interfaces;

use WorldFactory\QQ\Application;
use WorldFactory\QQ\Misc\BasicCommand;
use Symfony\Component\Console\Output\OutputInterface;

interface RunnerInterface
{

    public function run(string $script) : void;

    public function format(string $script) : string;

    public function setOutput(OutputInterface $output) : RunnerInterface;

    public function getOutput() : OutputInterface;

    public function setApplication(Application $application) : RunnerInterface;

    public function getApplication() : Application;

    public function setCommand(BasicCommand $command) : RunnerInterface;

    public function getCommand() : BasicCommand;
}