<?php

namespace WorldFactory\QQ\Services\StageBuilders;

use WorldFactory\QQ\Components\Context;
use WorldFactory\QQ\Components\Stages\TryCatchStage;
use WorldFactory\QQ\Components\Steps\TryCatchStep;
use WorldFactory\QQ\Foundations\AbstractStage;
use WorldFactory\QQ\Foundations\AbstractStageBuilder;
use WorldFactory\QQ\Foundations\AbstractStep;

class TryCatchStageBuilder extends AbstractStageBuilder
{
    public function isValid(AbstractStep $step): bool
    {
        return $step instanceof TryCatchStep;
    }

    /**
     * @param TryCatchStep $step
     * @param Context $context
     * @return AbstractStage
     */
    public function buildStage($step, Context $context) : AbstractStage
    {
        return new TryCatchStage($step, $context->getOutput());
    }
}
