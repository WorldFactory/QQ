<?php

namespace WorldFactory\QQ\Services\StepBuilders;

use WorldFactory\QQ\Misc\OptionBag;
use WorldFactory\QQ\Foundations\AbstractStep;
use WorldFactory\QQ\Entities\Steps\ChildStep;
use WorldFactory\QQ\Foundations\AbstractStepBuilder;

class ChildStepBuilder extends  AbstractStepBuilder
{
    public function isValid($definition) : bool
    {
        return (is_array($definition) && array_key_exists('run', $definition));
    }

    public function build($definition, OptionBag $config) : AbstractStep
    {
        return new ChildStep($this->getStepFactory(), $config, $definition);
    }
}