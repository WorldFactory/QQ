<?php

namespace WorldFactory\QQ\Services\StepBuilders;

use WorldFactory\QQ\Misc\OptionBag;
use WorldFactory\QQ\Foundations\AbstractStep;
use WorldFactory\QQ\Entities\Steps\LeafStep;
use WorldFactory\QQ\Foundations\AbstractStepBuilder;

class LeafStepBuilder extends  AbstractStepBuilder
{
    public function isValid($definition) : bool
    {
        return is_string($definition);
    }

    public function build($definition, OptionBag $config) : AbstractStep
    {
        return new LeafStep($this->getStepFactory(), $config, $definition);
    }
}