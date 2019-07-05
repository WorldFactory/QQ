<?php

namespace WorldFactory\QQ\Services\StageBuilders;

use WorldFactory\QQ\Components\Context;
use WorldFactory\QQ\Components\Stages\AddStage;
use WorldFactory\QQ\Components\Steps\AddStep;
use WorldFactory\QQ\Foundations\AbstractStage;
use WorldFactory\QQ\Foundations\AbstractStageBuilder;
use WorldFactory\QQ\Foundations\AbstractStep;

class AddStageBuilder extends AbstractStageBuilder
{
    public function isValid(AbstractStep $step): bool
    {
        return $step instanceof AddStep;
    }

    public function buildStage($step, Context $context): AbstractStage
    {
        return new AddStage($step);
    }
}
