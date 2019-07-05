<?php

namespace WorldFactory\QQ\Services\StageBuilders;

use WorldFactory\QQ\Components\Context;
use WorldFactory\QQ\Components\Stages\IfThenElseStage;
use WorldFactory\QQ\Components\Steps\IfThenElseStep;
use WorldFactory\QQ\Foundations\AbstractStage;
use WorldFactory\QQ\Foundations\AbstractStageBuilder;
use WorldFactory\QQ\Foundations\AbstractStep;

class IfThenElseStageBuilder extends AbstractStageBuilder
{
    public function isValid(AbstractStep $step): bool
    {
        return $step instanceof IfThenElseStep;
    }

    /**
     * @param IfThenElseStep $step
     * @param Context $context
     * @return AbstractStage
     */
    public function buildStage($step, Context $context) : AbstractStage
    {
        return new IfThenElseStage($step, $context->getOutput());
    }
}
