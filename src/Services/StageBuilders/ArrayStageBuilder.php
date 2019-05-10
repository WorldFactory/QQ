<?php

namespace WorldFactory\QQ\Services\StageBuilders;

use WorldFactory\QQ\Entities\Context;
use WorldFactory\QQ\Entities\Stages\ArrayStage;
use WorldFactory\QQ\Entities\Steps\ArrayStep;
use WorldFactory\QQ\Foundations\AbstractStage;
use WorldFactory\QQ\Foundations\AbstractStageBuilder;
use WorldFactory\QQ\Foundations\AbstractStep;

class ArrayStageBuilder extends AbstractStageBuilder
{
    public function isValid(AbstractStep $step): bool
    {
        return $step instanceof ArrayStep;
    }

    public function buildStage($step, Context $context): AbstractStage
    {
        return new ArrayStage($step);
    }
}
