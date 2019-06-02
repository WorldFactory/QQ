<?php

namespace WorldFactory\QQ\Entities\Stages;

use Exception;
use WorldFactory\QQ\Foundations\AbstractStage;
use WorldFactory\QQ\Entities\Steps\AndStep;
use WorldFactory\QQ\Misc\StepWalker;

/**
 * Class AndStage
 * @package WorldFactory\QQ\Entities\Stages
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
