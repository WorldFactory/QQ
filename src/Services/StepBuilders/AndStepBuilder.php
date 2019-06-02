<?php

namespace WorldFactory\QQ\Services\StepBuilders;

use WorldFactory\QQ\Misc\OptionBag;
use WorldFactory\QQ\Foundations\AbstractStep;
use WorldFactory\QQ\Entities\Steps\AndStep;
use WorldFactory\QQ\Foundations\AbstractStepBuilder;

class AndStepBuilder extends  AbstractStepBuilder
{
    public function isValid($definition) : bool
    {
        return (is_array($definition) && array_key_exists('and', $definition));
    }

    public function build($definition, OptionBag $config) : AbstractStep
    {
        return new AndStep($this->getStepFactory(), $config, $definition);
    }
}