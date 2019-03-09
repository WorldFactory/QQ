<?php

namespace WorldFactory\QQ\Services\StageBuilders;

use WorldFactory\QQ\Entities\Context;
use WorldFactory\QQ\Entities\Stages\ConditionStage;
use WorldFactory\QQ\Entities\Steps\ConditionStep;
use WorldFactory\QQ\Foundations\AbstractStage;
use WorldFactory\QQ\Foundations\AbstractStageBuilder;
use WorldFactory\QQ\Foundations\AbstractStep;

class ConditionStageBuilder extends AbstractStageBuilder
{
    public function isValid(AbstractStep $step): bool
    {
        return $step instanceof ConditionStep;
    }

    public function build(AbstractStep $step, Context $context): AbstractStage
    {
        return new ConditionStage($step);
    }
}