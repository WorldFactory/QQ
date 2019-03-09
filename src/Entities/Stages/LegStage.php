<?php

namespace WorldFactory\QQ\Entities\Stages;

use WorldFactory\QQ\Entities\Steps\LegStep;
use WorldFactory\QQ\Foundations\AbstractStage;
use WorldFactory\QQ\Misc\StepWalker;

/**
 * Class ArrayStage
 * @package WorldFactory\QQ\Entities\Stages
 *
 * @method LegStep getStep()
 */
class LegStage extends AbstractStage
{
    /**
     * @inheritdoc
     */
    public function execute(StepWalker $stepWalker) : bool
    {
        $stepWalker->walk($this->getStep()->getChild());

        return true;
    }
}
