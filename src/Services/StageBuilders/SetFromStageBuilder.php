<?php

namespace WorldFactory\QQ\Services\StageBuilders;

use WorldFactory\QQ\Components\Context;
use WorldFactory\QQ\Components\Stages\SetFromStage;
use WorldFactory\QQ\Components\Steps\SetFromStep;
use WorldFactory\QQ\Foundations\AbstractStage;
use WorldFactory\QQ\Foundations\AbstractStageBuilder;
use WorldFactory\QQ\Foundations\AbstractStep;

class SetFromStageBuilder extends AbstractStageBuilder
{
    public function isValid(AbstractStep $step): bool
    {
        return $step instanceof SetFromStep;
    }

    /**
     * @param SetFromStep $step
     * @param Context $context
     * @return AbstractStage
     */
    public function buildStage($step, Context $context) : AbstractStage
    {
        return new SetFromStage($step, $context, $context->getOutput());
    }
}
