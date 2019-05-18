<?php

namespace WorldFactory\QQ\Entities\Steps;

use Exception;
use WorldFactory\QQ\Misc\OptionBag;
use WorldFactory\QQ\Foundations\AbstractStep;
use WorldFactory\QQ\Services\StepFactory;

class ChildStep extends AbstractStep
{
    protected $child;

    /**
     * ChildStep constructor.
     * @param StepFactory $stepFactory
     * @param OptionBag $config
     * @param array $definition
     * @throws Exception
     */
    public function __construct(StepFactory $stepFactory, OptionBag $config, array $definition)
    {
        parent::__construct($stepFactory, $config);

        $this->child = $stepFactory->buildStep($definition['script'], $this->getOptionBag());
    }

    /**
     * @return AbstractStep|null
     */
    public function getChild() : AbstractStep
    {
        return $this->child;
    }
}