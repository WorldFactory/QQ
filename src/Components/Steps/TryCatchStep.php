<?php

namespace WorldFactory\QQ\Components\Steps;

use Exception;
use WorldFactory\QQ\Misc\OptionBag;
use WorldFactory\QQ\Foundations\AbstractStep;
use WorldFactory\QQ\Services\StepFactory;

class TryCatchStep extends AbstractStep
{
    private $try;

    private $catch = null;

    /**
     * IfThenElseStep constructor.
     * @param StepFactory $stepFactory
     * @param OptionBag $config
     * @param array $definition
     * @throws Exception
     */
    public function __construct(StepFactory $stepFactory, OptionBag $config, array $definition)
    {
        parent::__construct($stepFactory, $config);

        $this->try = $stepFactory->buildStep($definition['try'], $this->getOptionBag());

        if (array_key_exists('catch', $definition)) {
            $this->catch = $stepFactory->buildStep($definition['catch'], $this->getOptionBag());
        }
    }

    /**
     * @return AbstractStep
     */
    public function getTry()
    {
        return $this->try;
    }

    /**
     * @return AbstractStep|null
     */
    public function getCatch() :? AbstractStep
    {
        return $this->catch;
    }
}