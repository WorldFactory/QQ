<?php

namespace WorldFactory\QQ\Entities\Steps;

use Exception;
use WorldFactory\QQ\Entities\RunnerConfig;
use WorldFactory\QQ\Services\StepFactory;

class ConditionStep extends AbstractStep
{
    private $if;

    private $then;

    private $else = null;

    public function __construct(StepFactory $stepFactory, RunnerConfig $runnerConfig, array $definition)
    {
        parent::__construct($stepFactory, $runnerConfig);

        $this->if = $definition['if'];

        if (array_key_exists('then', $definition)) {
            $this->then = $stepFactory->buildStep($definition['then'], $runnerConfig);
        } else {
            throw new Exception("'Then' statement not provided.");
        }

        if (array_key_exists('else', $definition)) {
            $this->then = $stepFactory->buildStep($definition['else'], $runnerConfig);
        }
    }

    /**
     * @return mixed
     */
    public function getIf()
    {
        return $this->if;
    }

    /**
     * @return AbstractStep|null
     */
    public function getThen(): ?AbstractStep
    {
        return $this->then;
    }

    /**
     * @return null
     */
    public function getElse()
    {
        return $this->else;
    }
}