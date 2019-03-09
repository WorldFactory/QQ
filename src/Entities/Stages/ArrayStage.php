<?php

namespace WorldFactory\QQ\Entities\Stages;

use WorldFactory\QQ\Foundations\AbstractStage;
use WorldFactory\QQ\Entities\Steps\ArrayStep;
use WorldFactory\QQ\Misc\StepWalker;

/**
 * Class ArrayStage
 * @package WorldFactory\QQ\Entities\Stages
 *
 * @method ArrayStep getStep()
 */
class ArrayStage extends AbstractStage
{
    /**
     * @inheritdoc
     */
    public function execute(StepWalker $stepWalker) : bool
    {
        foreach($this->getStep()->getChildren() as $step) {
            $stepWalker->walk($step);
        }

        return true;
    }
}
