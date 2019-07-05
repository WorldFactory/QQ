<?php

namespace WorldFactory\QQ\Services\StepBuilders;

use WorldFactory\QQ\Misc\OptionBag;
use WorldFactory\QQ\Foundations\AbstractStep;
use WorldFactory\QQ\Components\Steps\AddStep;
use WorldFactory\QQ\Foundations\AbstractStepBuilder;

class AddStepBuilder extends  AbstractStepBuilder
{
    public function isValid($definition) : bool
    {
        return (is_array($definition) && array_key_exists('add', $definition));
    }

    public function build($definition, OptionBag $config) : AbstractStep
    {
        return new AddStep($this->getStepFactory(), $config, $definition);
    }
}