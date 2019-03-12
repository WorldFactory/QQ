<?php

namespace WorldFactory\QQ\Services\StepBuilders;

use WorldFactory\QQ\Misc\OptionBag;
use WorldFactory\QQ\Foundations\AbstractStep;
use WorldFactory\QQ\Entities\Steps\ArrayStep;
use WorldFactory\QQ\Foundations\AbstractStepBuilder;

class ArrayStepBuilder extends  AbstractStepBuilder
{
    public function isValid($definition) : bool
    {
        return (is_array($definition) && $this->isSequential($definition));
    }

    private function isSequential(array $array)
    {
        return (array_keys($array) === range(0, count($array) - 1));
    }

    public function build($definition, OptionBag $config) : AbstractStep
    {
        return new ArrayStep($this->getStepFactory(), $config, $definition);
    }
}