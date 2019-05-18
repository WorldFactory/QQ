<?php

namespace WorldFactory\QQ\Services\StageBuilders;

use WorldFactory\QQ\Entities\Context;
use WorldFactory\QQ\Entities\Stages\ChildStage;
use WorldFactory\QQ\Entities\Steps\ChildStep;
use WorldFactory\QQ\Foundations\AbstractStage;
use WorldFactory\QQ\Foundations\AbstractStageBuilder;
use WorldFactory\QQ\Foundations\AbstractStep;

class ChildStageBuilder extends AbstractStageBuilder
{
    public function isValid(AbstractStep $step): bool
    {
        return $step instanceof ChildStep;
    }

    public function buildStage($step, Context $context): AbstractStage
    {
        return new ChildStage($step);
    }
}
