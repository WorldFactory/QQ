<?php

namespace WorldFactory\QQ\Services\StageBuilders;

use WorldFactory\QQ\Components\Context;
use WorldFactory\QQ\Components\Stages\ForInDoStage;
use WorldFactory\QQ\Components\Steps\ForInDoStep;
use WorldFactory\QQ\Foundations\AbstractStage;
use WorldFactory\QQ\Foundations\AbstractStageBuilder;
use WorldFactory\QQ\Foundations\AbstractStep;

class ForInDoStageBuilder extends AbstractStageBuilder
{
    public function isValid(AbstractStep $step): bool
    {
        return $step instanceof ForInDoStep;
    }

    /**
     * @param ForInDoStep $step
     * @param Context $context
     * @return AbstractStage
     */
    public function buildStage($step, Context $context) : AbstractStage
    {
        return new ForInDoStage($step, $context, $context->getOutput());
    }
}
