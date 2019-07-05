<?php

namespace WorldFactory\QQ\Components\Stages;

use Exception;
use WorldFactory\QQ\Components\Steps\RunStep;
use WorldFactory\QQ\Foundations\AbstractStage;
use WorldFactory\QQ\Misc\StepWalker;

/**
 * Class RunStage
 * @package WorldFactory\QQ\Components\Stages
 *
 * @method RunStep getStep()
 */
class RunStage extends AbstractStage
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
