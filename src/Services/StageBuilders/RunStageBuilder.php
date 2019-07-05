<?php

namespace WorldFactory\QQ\Services\StageBuilders;

use WorldFactory\QQ\Components\Context;
use WorldFactory\QQ\Entities\Stages\RunStage;
use WorldFactory\QQ\Entities\Steps\RunStep;
use WorldFactory\QQ\Foundations\AbstractStage;
use WorldFactory\QQ\Foundations\AbstractStageBuilder;
use WorldFactory\QQ\Foundations\AbstractStep;

class RunStageBuilder extends AbstractStageBuilder
{
    public function isValid(AbstractStep $step): bool
    {
        return $step instanceof RunStep;
    }

    public function buildStage($step, Context $context): AbstractStage
    {
        return new RunStage($step);
    }
}
