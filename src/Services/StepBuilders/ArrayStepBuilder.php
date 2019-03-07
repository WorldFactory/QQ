<?php

namespace WorldFactory\QQ\Services\StepBuilders;

use WorldFactory\QQ\Entities\RunnerConfig;
use WorldFactory\QQ\Foundations\AbstractStep;
use WorldFactory\QQ\Entities\Steps\ArrayStep;

class ArrayStepBuilder extends  AbstractStepBuilder
{
    public function isValid($definition) : bool
    {
        return (is_array($definition) && $this->isSequential($definition));
    }

    private function isSequential(array $array)
    {
        return (array_keys($array) === range(0, count($array) - 1));
    }

    public function build($definition, RunnerConfig $runnerConfig) : AbstractStep
    {
        return new ArrayStep($this->getStepFactory(), $runnerConfig, $definition);
    }
}