<?php

namespace WorldFactory\QQ\Services\StepBuilders;

use WorldFactory\QQ\Misc\OptionBag;
use WorldFactory\QQ\Foundations\AbstractStep;
use WorldFactory\QQ\Components\Steps\StringStep;
use WorldFactory\QQ\Foundations\AbstractStepBuilder;

class StringStepBuilder extends  AbstractStepBuilder
{
    public function isValid($definition) : bool
    {
        return is_string($definition);
    }

    public function build($definition, OptionBag $config) : AbstractStep
    {
        return new StringStep($this->getStepFactory(), $config, $definition);
    }
}