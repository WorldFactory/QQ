<?php

namespace WorldFactory\QQ\Entities\Stages;

use WorldFactory\QQ\Entities\Steps\ChildStep;
use WorldFactory\QQ\Foundations\AbstractStage;
use WorldFactory\QQ\Misc\StepWalker;

/**
 * Class ChildStage
 * @package WorldFactory\QQ\Entities\Stages
 *
 * @method ChildStep getStep()
 */
class ChildStage extends AbstractStage
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
