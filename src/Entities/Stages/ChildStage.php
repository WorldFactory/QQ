<?php

namespace WorldFactory\QQ\Entities\Stages;

use Exception;
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
     * @throws Exception
     */
    public function execute(StepWalker $stepWalker)
    {
        return $stepWalker->walk($this->getStep()->getChild());
    }
}
