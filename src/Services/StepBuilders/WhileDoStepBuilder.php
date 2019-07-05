<?php

namespace WorldFactory\QQ\Services\StepBuilders;

use WorldFactory\QQ\Misc\OptionBag;
use WorldFactory\QQ\Foundations\AbstractStep;
use WorldFactory\QQ\Components\Steps\WhileDoStep;
use WorldFactory\QQ\Foundations\AbstractStepBuilder;

class WhileDoStepBuilder extends AbstractStepBuilder
{
    public function isValid($definition) : bool
    {
        return (is_array($definition) && array_key_exists('while', $definition));
    }

    public function build($definition, OptionBag $config) : AbstractStep
    {
        return new WhileDoStep($this->getStepFactory(), $config, $definition);
    }
}