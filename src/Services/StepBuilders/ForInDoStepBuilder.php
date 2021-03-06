<?php

namespace WorldFactory\QQ\Services\StepBuilders;

use WorldFactory\QQ\Misc\OptionBag;
use WorldFactory\QQ\Foundations\AbstractStep;
use WorldFactory\QQ\Components\Steps\ForInDoStep;
use WorldFactory\QQ\Foundations\AbstractStepBuilder;

class ForInDoStepBuilder extends AbstractStepBuilder
{
    public function isValid($definition) : bool
    {
        return (is_array($definition) && array_key_exists('for', $definition));
    }

    public function build($definition, OptionBag $config) : AbstractStep
    {
        return new ForInDoStep($this->getStepFactory(), $config, $definition);
    }
}