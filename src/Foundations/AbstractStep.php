<?php

namespace WorldFactory\QQ\Foundations;

use WorldFactory\QQ\Misc\OptionBag;
use WorldFactory\QQ\Services\StepFactory;

abstract class AbstractStep
{
    /** @var StepFactory */
    private $stepFactory;

    /** @var OptionBag */
    private $config;

    public function __construct(StepFactory $stepFactory, OptionBag $config)
    {
        $this->stepFactory = $stepFactory;
        $this->config = $config;
    }

    /**
     * @return StepFactory
     */
    public function getStepFactory(): StepFactory
    {
        return $this->stepFactory;
    }

    /**
     * @return OptionBag
     */
    public function getOptionBag(): OptionBag
    {
        return $this->config;
    }
}