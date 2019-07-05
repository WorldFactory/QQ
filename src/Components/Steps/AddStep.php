<?php

namespace WorldFactory\QQ\Components\Steps;

use Exception;
use WorldFactory\QQ\Misc\OptionBag;
use WorldFactory\QQ\Foundations\AbstractStep;
use WorldFactory\QQ\Services\StepFactory;

class AddStep extends AbstractStep
{
    private $children = [];

    /**
     * AndStep constructor.
     * @param StepFactory $stepFactory
     * @param OptionBag $config
     * @param array $definition
     * @throws Exception
     */
    public function __construct(StepFactory $stepFactory, OptionBag $config, array $definition)
    {
        parent::__construct($stepFactory, $config);

        if(!is_array($definition['add'])) {
            throw new Exception("The 'add' key must be an Array.");
        }

        foreach($definition['add'] as $line) {
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