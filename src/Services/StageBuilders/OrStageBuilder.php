<?php

namespace WorldFactory\QQ\Services\StageBuilders;

use WorldFactory\QQ\Entities\Context;
use WorldFactory\QQ\Entities\Stages\OrStage;
use WorldFactory\QQ\Entities\Steps\OrStep;
use WorldFactory\QQ\Foundations\AbstractStage;
use WorldFactory\QQ\Foundations\AbstractStageBuilder;
use WorldFactory\QQ\Foundations\AbstractStep;

class OrStageBuilder extends AbstractStageBuilder
{
    public function isValid(AbstractStep $step): bool
    {
        return $step instanceof OrStep;
    }

    public function buildStage($step, Context $context): AbstractStage
    {
        return new OrStage($step);
    }
}
