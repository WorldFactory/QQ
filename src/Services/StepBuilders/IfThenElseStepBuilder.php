<?php

namespace WorldFactory\QQ\Services\StepBuilders;

use WorldFactory\QQ\Misc\OptionBag;
use WorldFactory\QQ\Foundations\AbstractStep;
use WorldFactory\QQ\Entities\Steps\IfThenElseStep;
use WorldFactory\QQ\Foundations\AbstractStepBuilder;

class IfThenElseStepBuilder extends AbstractStepBuilder
{
    public function isValid($definition) : bool
    {
        return (is_array($definition) && array_key_exists('if', $definition));
    }

    public function build($definition, OptionBag $config) : AbstractStep
    {
        return new IfThenElseStep($this->getStepFactory(), $config, $definition);
    }
}