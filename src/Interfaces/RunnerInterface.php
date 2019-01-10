<?php

namespace WorldFactory\QQ\Interfaces;

use WorldFactory\QQ\Application;
use WorldFactory\QQ\Misc\BasicCommand;
use Symfony\Component\Console\Output\OutputInterface;
use WorldFactory\QQ\Misc\ConfigLoader;

interface RunnerInterface
{

    public function run(string $script) : void;

    public function format(string $script) : string;

    public function setOutput(OutputInterface $output) : RunnerInterface;

    public function getOutput() : OutputInterface;

    public function getApplication() : Application;

    public function getConfigLoader() : ConfigLoader;

    public function setCommand(BasicCommand $command) : RunnerInterface;

    public function getCommand() : BasicCommand;
}