<?php

namespace WorldFactory\QQ\Interfaces;

use WorldFactory\QQ\Application;
use WorldFactory\QQ\Misc\BasicCommand;
use Symfony\Component\Console\Output\OutputInterface;

interface RunnerInterface
{

    public function run(string $script);

    public function format(string $script) : string;

    public function setOutput(OutputInterface $output);

    public function getOutput();

    public function setApplication(Application $application);

    public function getApplication();

    public function setCommand(BasicCommand $command);

    public function getCommand();
}