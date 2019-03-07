<?php

namespace WorldFactory\QQ\Services\StepBuilders;

use WorldFactory\QQ\Entities\RunnerConfig;
use WorldFactory\QQ\Foundations\AbstractStep;
use WorldFactory\QQ\Entities\Steps\LeafStep;
use WorldFactory\QQ\Foundations\AbstractStepBuilder;

class LeafStepBuilder extends  AbstractStepBuilder
{
    public function isValid($definition) : bool
    {
        return is_string($definition);
    }

    public function build($definition, RunnerConfig $runnerConfig) : AbstractStep
    {
        return new LeafStep($this->getStepFactory(), $runnerConfig, $definition);
    }
}