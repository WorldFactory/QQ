<?php

namespace WorldFactory\QQ\Entities\Steps;

use Exception;
use WorldFactory\QQ\Entities\RunnerConfig;
use WorldFactory\QQ\Foundations\AbstractStep;
use WorldFactory\QQ\Services\StepFactory;

class LeafStep extends AbstractStep
{
    protected $script;

    /**
     * LeafStep constructor.
     * @param StepFactory $stepFactory
     * @param RunnerConfig $runnerConfig
     * @param string $script
     */
    public function __construct(StepFactory $stepFactory, RunnerConfig $runnerConfig, string $script)
    {
        parent::__construct($stepFactory, $runnerConfig);

        $this->script = $script;
    }

    /**
     * @return string
     */
    public function getScript(): string
    {
        return $this->script;
    }
}