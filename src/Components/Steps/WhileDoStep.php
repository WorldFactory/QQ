<?php

namespace WorldFactory\QQ\Components\Steps;

use Exception;
use WorldFactory\QQ\Misc\OptionBag;
use WorldFactory\QQ\Foundations\AbstractStep;
use WorldFactory\QQ\Services\StepFactory;

class WhileDoStep extends AbstractStep
{
    private $condition;

    private $while;

    private $do;

    /**
     * WhileDoStep constructor.
     * @param StepFactory $stepFactory
     * @param OptionBag $config
     * @param array $definition
     * @throws Exception
     */
    public function __construct(StepFactory $stepFactory, OptionBag $config, array $definition)
    {
        parent::__construct($stepFactory, $config);

        $this->condition = $definition['while'];

        $this->while = $stepFactory->buildStep($definition['while'], $this->buildIndividualOptionBag());

        if (array_key_exists('do', $definition)) {
            $this->do = $stepFactory->buildStep($definition['do'], $this->getOptionBag());
        } else {
            throw new Exception("'Do' statement not provided.");
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
    public function getWhile()
    {
        return $this->while;
    }

    /**
     * @return AbstractStep
     */
    public function getDo() : AbstractStep
    {
        return $this->do;
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
