<?php

namespace WorldFactory\QQ\Misc;

use Exception;
use WorldFactory\QQ\Components\Context;
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

    /**
     * @param AbstractStep $step
     * @return mixed
     * @throws Exception
     */
    public function walk(AbstractStep $step)
    {
        /** @var AbstractStage $stage */
        $stage = $this->stageFactory->buildStage($step, $this->context);

        return $stage->execute($this);
    }
}