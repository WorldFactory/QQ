<?php

namespace WorldFactory\QQ\Components\Stages;

use Exception;
use WorldFactory\QQ\Foundations\AbstractStage;
use WorldFactory\QQ\Components\Steps\AddStep;
use WorldFactory\QQ\Misc\StepWalker;

/**
 * Class AddStage
 * @package WorldFactory\QQ\Components\Stages
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
