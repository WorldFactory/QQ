<?php

namespace WorldFactory\QQ\Components\Stages;

use Exception;
use WorldFactory\QQ\Foundations\AbstractStage;
use WorldFactory\QQ\Components\Steps\ArrayStep;
use WorldFactory\QQ\Misc\StepWalker;

/**
 * Class ArrayStage
 * @package WorldFactory\QQ\Components\Stages
 *
 * @method ArrayStep getStep()
 */
class ArrayStage extends AbstractStage
{
    /**
     * @inheritdoc
     * @throws Exception
     */
    public function execute(StepWalker $stepWalker)
    {
        $result = null;

        foreach($this->getStep()->getChildren() as $step) {
            $result = $stepWalker->walk($step);
        }

        return $result;
    }
}
