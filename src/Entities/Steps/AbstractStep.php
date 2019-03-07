<?php

namespace WorldFactory\QQ\Entities\Steps;

use WorldFactory\QQ\Entities\RunnerConfig;
use WorldFactory\QQ\Services\StepFactory;

abstract class AbstractStep
{
    /** @var StepFactory */
    private $stepFactory;

    /** @var RunnerConfig */
    private $runnerConfig;

    public function __construct(StepFactory $stepFactory, RunnerConfig $runnerConfig)
    {
        $this->stepFactory = $stepFactory;
        $this->runnerConfig = $runnerConfig;
    }

    /**
     * @return StepFactory
     */
    public function getStepFactory(): StepFactory
    {
        return $this->stepFactory;
    }

    /**
     * @return RunnerConfig
     */
    public function getRunnerConfig(): RunnerConfig
    {
        return $this->runnerConfig;
    }
}