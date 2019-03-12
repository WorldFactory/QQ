<?php

namespace WorldFactory\QQ\Foundations;

use WorldFactory\QQ\Misc\OptionBag;
use WorldFactory\QQ\Services\StepFactory;

abstract class AbstractStepBuilder
{
    /** @var StepFactory */
    private $stepFactory;

    public function setStepFactory(StepFactory $stepFactory) : self
    {
        $this->stepFactory = $stepFactory;

        return $this;
    }

    /**
     * @return StepFactory
     */
    public function getStepFactory(): StepFactory
    {
        return $this->stepFactory;
    }

    abstract public function isValid($definition) : bool;

    abstract public function build($definition, OptionBag $config) : AbstractStep;
}