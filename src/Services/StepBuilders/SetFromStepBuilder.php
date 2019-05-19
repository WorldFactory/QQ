<?php

namespace WorldFactory\QQ\Services\StepBuilders;

use WorldFactory\QQ\Misc\OptionBag;
use WorldFactory\QQ\Foundations\AbstractStep;
use WorldFactory\QQ\Entities\Steps\SetFromStep;
use WorldFactory\QQ\Foundations\AbstractStepBuilder;

class SetFromStepBuilder extends AbstractStepBuilder
{
    public function isValid($definition) : bool
    {
        return (is_array($definition) && array_key_exists('set', $definition) && array_key_exists('from', $definition));
    }

    public function build($definition, OptionBag $config) : AbstractStep
    {
        return new SetFromStep($this->getStepFactory(), $config, $definition);
    }
}