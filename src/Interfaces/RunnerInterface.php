<?php

namespace WorldFactory\QQ\Interfaces;

use Symfony\Component\Console\Output\OutputInterface;
use WorldFactory\QQ\Misc\RunnerOptionBag;

interface RunnerInterface
{
    public function getOptionDefinitions() : array;

    public function setOptions(RunnerOptionBag $options);

    public function getShortDescription() : string;

    public function getLongDescription() : string;

    public function run(string $script) : void;

    public function format(string $compiledScript) : string;

    public function setInput(TokenizedInputInterface $input) : RunnerInterface;

    public function getInput() : TokenizedInputInterface;

    public function setOutput(OutputInterface $output) : RunnerInterface;

    public function getOutput() : OutputInterface;

    public function isHeaderDisplayed() : bool;
}