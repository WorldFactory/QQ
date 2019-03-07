<?php

namespace WorldFactory\QQ\Entities\Steps;

use WorldFactory\QQ\Entities\RunnerConfig;
use WorldFactory\QQ\Services\StepFactory;

class LegStep extends AbstractStep
{
    protected $child;

    public function __construct(StepFactory $stepFactory, RunnerConfig $runnerConfig, array $definition)
    {
        parent::__construct($stepFactory, $runnerConfig);

        $this->child = $stepFactory->buildStep($definition['script'], $runnerConfig);
    }

    /**
     * @return AbstractStep|null
     */
    public function getChild() : AbstractStep
    {
        return $this->child;
    }
}