<?php

namespace WorldFactory\QQ\Misc;


use WorldFactory\QQ\Entities\Context;
use WorldFactory\QQ\Foundations\AbstractStage;
use WorldFactory\QQ\Foundations\AbstractStep;
use WorldFactory\QQ\Services\StageFactory;

class StepWalker
{
    /** @var StageFactory */
    private $stageFactory;

    /** @var Context */
    private $context;

    public function __construct(StageFactory $stageFactory, Context $context)
    {
        $this->stageFactory = $stageFactory;
        $this->context = $context;
    }

    public function walk(AbstractStep $step)
    {
        /** @var AbstractStage $stage */
        $stage = $this->stageFactory->buildStage($step, $this->context);

        $report = $stage->execute($this);

        return $report;
    }
}