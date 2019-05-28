<?php

namespace WorldFactory\QQ\Entities\Steps;

use Exception;
use WorldFactory\QQ\Misc\OptionBag;
use WorldFactory\QQ\Foundations\AbstractStep;
use WorldFactory\QQ\Services\StepFactory;

class SetFromStep extends AbstractStep
{
    private $set;

    private $from;

    /**
     * SetFromStep constructor.
     * @param StepFactory $stepFactory
     * @param OptionBag $config
     * @param array $definition
     * @throws Exception
     * @todo Verify 'set' key validity.
     */
    public function __construct(StepFactory $stepFactory, OptionBag $config, array $definition)
    {
        parent::__construct($stepFactory, $config);

        $this->set = $definition['set'];

        $this->from = $stepFactory->buildStep($definition['from'], $this->getOptionBag());
    }

    /**
     * @return string
     */
    public function getSet()
    {
        return $this->set;
    }

    /**
     * @return AbstractStep|null
     */
    public function getFrom() :? AbstractStep
    {
        return $this->from;
    }
}