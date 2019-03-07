<?php

namespace WorldFactory\QQ\Services\StepBuilders;

use WorldFactory\QQ\Entities\RunnerConfig;
use WorldFactory\QQ\Foundations\AbstractStep;
use WorldFactory\QQ\Entities\Steps\LegStep;
use WorldFactory\QQ\Foundations\AbstractStepBuilder;

class LegStepBuilder extends  AbstractStepBuilder
{
    public function isValid($definition) : bool
    {
        return (is_array($definition) && array_key_exists('script', $definition));
    }

    public function build($definition, RunnerConfig $runnerConfig) : AbstractStep
    {
        return new LegStep($this->getStepFactory(), $runnerConfig, $definition);
    }
}