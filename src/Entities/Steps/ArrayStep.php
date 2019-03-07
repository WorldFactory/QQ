<?php

namespace WorldFactory\QQ\Entities\Steps;

use WorldFactory\QQ\Entities\RunnerConfig;
use WorldFactory\QQ\Services\StepFactory;

class ArrayStep extends AbstractStep
{
    private $children = [];

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