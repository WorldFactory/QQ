<?php

namespace WorldFactory\QQ\Entities\Steps;

use Exception;
use WorldFactory\QQ\Misc\OptionBag;
use WorldFactory\QQ\Foundations\AbstractStep;
use WorldFactory\QQ\Services\StepFactory;

class ArrayStep extends AbstractStep
{
    private $children = [];

    /**
     * ArrayStep constructor.
     * @param StepFactory $stepFactory
     * @param OptionBag $config
     * @param array $definition
     * @throws Exception
     */
    public function __construct(StepFactory $stepFactory, OptionBag $config, array $definition)
    {
        parent::__construct($stepFactory, $config);

        foreach($definition as $line) {
            $this->children[] = $stepFactory->buildStep($line, $this->getOptionBag());
        }
    }

    /**
     * @return array
     */
    public function getChildren(): array
    {
        return $this->children;
    }
}