<?php

namespace WorldFactory\QQ\Entities\Stages;

use Exception;
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
