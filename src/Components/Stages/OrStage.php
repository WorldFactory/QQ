<?php

namespace WorldFactory\QQ\Components\Stages;

use Exception;
use WorldFactory\QQ\Foundations\AbstractStage;
use WorldFactory\QQ\Components\Steps\OrStep;
use WorldFactory\QQ\Misc\StepWalker;

/**
 * Class OrStage
 * @package WorldFactory\QQ\Components\Stages
 *
 * @method OrStep getStep()
 */
class OrStage extends AbstractStage
{
    /**
     * @inheritdoc
     * @throws Exception
     */
    public function execute(StepWalker $stepWalker)
    {
        foreach($this->getStep()->getChildren() as $step) {
            $result = $stepWalker->walk($step);

            if ($result === true) {
                return true;
            }
        }

        return false;
    }
}
