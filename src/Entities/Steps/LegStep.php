<?php

namespace WorldFactory\QQ\Entities\Steps;

use Exception;
use WorldFactory\QQ\Entities\RunnerConfig;
use WorldFactory\QQ\Foundations\AbstractStep;
use WorldFactory\QQ\Services\StepFactory;

class LegStep extends AbstractStep
{
    protected $child;

    /**
     * LegStep constructor.
     * @param StepFactory $stepFactory
     * @param RunnerConfig $runnerConfig
     * @param array $definition
     * @throws Exception
     */
    public function __construct(StepFactory $stepFactory, RunnerConfig $runnerConfig, array $definition)
    {
        parent::__construct($stepFactory, $runnerConfig);

        $this->child = $stepFactory->buildStep($definition['script'], $this->getRunnerConfig());
    }

    /**
     * @return AbstractStep|null
     */
    public function getChild() : AbstractStep
    {
        return $this->child;
    }
}