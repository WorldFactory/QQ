<?php

namespace WorldFactory\QQ\Entities\Stages;

use Exception;
use WorldFactory\QQ\Foundations\AbstractStage;
use WorldFactory\QQ\Entities\Steps\AddStep;
use WorldFactory\QQ\Misc\StepWalker;

/**
 * Class AddStage
 * @package WorldFactory\QQ\Entities\Stages
 *
 * @method AddStep getStep()
 */
class AddStage extends AbstractStage
{
    /**
     * @inheritdoc
     * @throws Exception
     */
    public function execute(StepWalker $stepWalker)
    {
        $result = '';

        foreach($this->getStep()->getChildren() as $step) {
            $result .= $stepWalker->walk($step);
        }

        return $result;
    }
}
