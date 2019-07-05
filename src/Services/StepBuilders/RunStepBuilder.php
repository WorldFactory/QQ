<?php

namespace WorldFactory\QQ\Services\StepBuilders;

use WorldFactory\QQ\Misc\OptionBag;
use WorldFactory\QQ\Foundations\AbstractStep;
use WorldFactory\QQ\Components\Steps\RunStep;
use WorldFactory\QQ\Foundations\AbstractStepBuilder;

class RunStepBuilder extends  AbstractStepBuilder
{
    public function isValid($definition) : bool
    {
        return (is_array($definition) && (array_key_exists('run', $definition) || array_key_exists('script', $definition)));
    }

    public function build($definition, OptionBag $config) : AbstractStep
    {
        return new RunStep($this->getStepFactory(), $config, $definition);
    }
}