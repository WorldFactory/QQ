<?php

namespace WorldFactory\QQ\Components\Stages;

use Exception;
use WorldFactory\QQ\Foundations\AbstractStage;
use WorldFactory\QQ\Components\Steps\AndStep;
use WorldFactory\QQ\Misc\StepWalker;

/**
 * Class AndStage
 * @package WorldFactory\QQ\Components\Stages
 *
 * @method AndStep getStep()
 */
class AndStage extends AbstractStage
{
    /**
     * @inheritdoc
     * @throws Exception
     */
    public function execute(StepWalker $stepWalker)
    {
        foreach($this->getStep()->getChildren() as $step) {
            $result = $stepWalker->walk($step);

            if ($result === false) {
                return false;
            }
        }

        return true;
    }
}
