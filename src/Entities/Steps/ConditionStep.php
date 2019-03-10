<?php

namespace WorldFactory\QQ\Entities\Steps;

use Exception;
use WorldFactory\QQ\Entities\RunnerConfig;
use WorldFactory\QQ\Foundations\AbstractStep;
use WorldFactory\QQ\Services\StepFactory;

class ConditionStep extends AbstractStep
{
    private $if;

    private $then;

    private $else = null;

    /**
     * ConditionStep constructor.
     * @param StepFactory $stepFactory
     * @param RunnerConfig $runnerConfig
     * @param array $definition
     * @throws Exception
     */
    public function __construct(StepFactory $stepFactory, RunnerConfig $runnerConfig, array $definition)
    {
        parent::__construct($stepFactory, $runnerConfig);

        $this->if = $definition['if'];

        if (array_key_exists('then', $definition)) {
            $this->then = $stepFactory->buildStep($definition['then'], $this->getRunnerConfig());
        } else {
            throw new Exception("'Then' statement not provided.");
        }

        if (array_key_exists('else', $definition)) {
            $this->else = $stepFactory->buildStep($definition['else'], $this->getRunnerConfig());
        }
    }

    /**
     * @return string
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
}