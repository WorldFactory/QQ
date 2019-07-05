<?php

namespace WorldFactory\QQ\Components\Steps;

use Exception;
use WorldFactory\QQ\Misc\OptionBag;
use WorldFactory\QQ\Foundations\AbstractStep;
use WorldFactory\QQ\Services\StepFactory;

class IfThenElseStep extends AbstractStep
{
    private $condition;

    private $if;

    private $then;

    private $else = null;

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

        $this->condition = $definition['if'];

        $this->if = $stepFactory->buildStep($definition['if'], $this->buildIndividualOptionBag());

        if (array_key_exists('then', $definition)) {
            $this->then = $stepFactory->buildStep($definition['then'], $this->getOptionBag());
        } else {
            throw new Exception("'Then' statement not provided.");
        }

        if (array_key_exists('else', $definition)) {
            $this->else = $stepFactory->buildStep($definition['else'], $this->getOptionBag());
        }
    }

    /**
     * @return mixed
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * @return AbstractStep
     */
    public function getIf()
    {
        return $this->if;
    }

    /**
     * @return AbstractStep|null
     */
    public function getThen() :? AbstractStep
    {
        return $this->then;
    }

    /**
     * @return AbstractStep|null
     */
    public function getElse() :? AbstractStep
    {
        return $this->else;
    }

    /**
     * @return OptionBag
     * @throws Exception
     */
    protected function buildIndividualOptionBag() : OptionBag
    {
        $options = new OptionBag([
            'type' => 'expr'
        ]);

        $options->addOptionDefinitions([
            'type'     => [
                'type' => 'string',
                'required' => true,
                'description' => "The default type to define which runner to be used."
            ]
        ]);

        return $options;
    }
}