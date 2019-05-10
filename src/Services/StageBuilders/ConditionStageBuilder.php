<?php

namespace WorldFactory\QQ\Services\StageBuilders;

use WorldFactory\QQ\Entities\Accreditor;
use WorldFactory\QQ\Entities\Context;
use WorldFactory\QQ\Entities\Stages\ConditionStage;
use WorldFactory\QQ\Entities\Steps\ConditionStep;
use WorldFactory\QQ\Foundations\AbstractStage;
use WorldFactory\QQ\Foundations\AbstractStageBuilder;
use WorldFactory\QQ\Foundations\AbstractStep;
use WorldFactory\QQ\Misc\ContextualizedFormatter;

class ConditionStageBuilder extends AbstractStageBuilder
{
    public function isValid(AbstractStep $step): bool
    {
        return $step instanceof ConditionStep;
    }

    /**
     * @param ConditionStep $step
     * @param Context $context
     * @return AbstractStage
     */
    public function buildStage($step, Context $context) : AbstractStage
    {
        $formatter = new ContextualizedFormatter($context);

        $accreditor = new Accreditor($step->getIf());

        $accreditor->compile($formatter);

        return new ConditionStage($step, $accreditor, $context->getOutput());
    }
}
