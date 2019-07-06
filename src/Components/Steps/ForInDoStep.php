<?php

namespace WorldFactory\QQ\Components\Steps;

use Exception;
use WorldFactory\QQ\Misc\OptionBag;
use WorldFactory\QQ\Foundations\AbstractStep;
use WorldFactory\QQ\Services\StepFactory;

class ForInDoStep extends AbstractStep
{
    private $source;

    private $for;

    private $in;

    private $do;

    /**
     * ForInDoStep constructor.
     * @param StepFactory $stepFactory
     * @param OptionBag $config
     * @param array $definition
     * @throws Exception
     */
    public function __construct(StepFactory $stepFactory, OptionBag $config, array $definition)
    {
        parent::__construct($stepFactory, $config);

        $this->for = $definition['for'];

        if (array_key_exists('in', $definition)) {
            $this->source = $definition['in'];
            $this->in = $stepFactory->buildStep($definition['in'], $this->getOptionBag());
        } else {
            throw new Exception("'In' statement not provided.");
        }

        if (array_key_exists('do', $definition)) {
            $this->do = $stepFactory->buildStep($definition['do'], $this->getOptionBag());
        } else {
            throw new Exception("'Do' statement not provided.");
        }
    }

    /**
     * @return mixed
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @return mixed
     */
    public function getFor()
    {
        return $this->for;
    }

    /**
     * @return AbstractStep
     */
    public function getIn() : AbstractStep
    {
        return $this->in;
    }

    /**
     * @return AbstractStep
     */
    public function getDo() : AbstractStep
    {
        return $this->do;
    }
}
