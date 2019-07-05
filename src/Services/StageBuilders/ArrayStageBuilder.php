<?php

namespace WorldFactory\QQ\Services\StageBuilders;

use WorldFactory\QQ\Components\Context;
use WorldFactory\QQ\Components\Stages\ArrayStage;
use WorldFactory\QQ\Components\Steps\ArrayStep;
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
