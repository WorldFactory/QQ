<?php

namespace WorldFactory\QQ\Entities\Steps;

use Exception;
use WorldFactory\QQ\Misc\OptionBag;
use WorldFactory\QQ\Foundations\AbstractStep;
use WorldFactory\QQ\Services\StepFactory;

class OrStep extends AbstractStep
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

        if(!is_array($definition['or'])) {
            throw new Exception("The 'or' key must be an Array.");
        }

        foreach($definition['or'] as $line) {
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