<?php

namespace WorldFactory\QQ\Entities\Steps;

use Exception;
use WorldFactory\QQ\Entities\RunnerConfig;
use WorldFactory\QQ\Foundations\AbstractStep;
use WorldFactory\QQ\Services\StepFactory;

class ArrayStep extends AbstractStep
{
    private $children = [];

    /**
     * ArrayStep constructor.
     * @param StepFactory $stepFactory
     * @param RunnerConfig $runnerConfig
     * @param array $definition
     * @throws Exception
     */
    public function __construct(StepFactory $stepFactory, RunnerConfig $runnerConfig, array $definition)
    {
        parent::__construct($stepFactory, $runnerConfig);

        foreach($definition as $line) {
            $this->children[] = $stepFactory->buildStep($line, $runnerConfig);
        }
    }

    /**
     * @return array
     */
    public function getChildren(): array
    {
        return $this->children;
    }
}