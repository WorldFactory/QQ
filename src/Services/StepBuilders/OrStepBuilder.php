<?php

namespace WorldFactory\QQ\Services\StepBuilders;

use WorldFactory\QQ\Misc\OptionBag;
use WorldFactory\QQ\Foundations\AbstractStep;
use WorldFactory\QQ\Entities\Steps\OrStep;
use WorldFactory\QQ\Foundations\AbstractStepBuilder;

class OrStepBuilder extends  AbstractStepBuilder
{
    public function isValid($definition) : bool
    {
        return (is_array($definition) && array_key_exists('or', $definition));
    }

    public function build($definition, OptionBag $config) : AbstractStep
    {
        return new OrStep($this->getStepFactory(), $config, $definition);
    }
}