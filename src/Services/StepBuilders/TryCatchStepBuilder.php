<?php

namespace WorldFactory\QQ\Services\StepBuilders;

use WorldFactory\QQ\Misc\OptionBag;
use WorldFactory\QQ\Foundations\AbstractStep;
use WorldFactory\QQ\Components\Steps\TryCatchStep;
use WorldFactory\QQ\Foundations\AbstractStepBuilder;

class TryCatchStepBuilder extends AbstractStepBuilder
{
    public function isValid($definition) : bool
    {
        return (is_array($definition) && array_key_exists('try', $definition));
    }

    public function build($definition, OptionBag $config) : AbstractStep
    {
        return new TryCatchStep($this->getStepFactory(), $config, $definition);
    }
}