<?php

namespace WorldFactory\QQ\Entities\Stages;

use Exception;
use WorldFactory\QQ\Entities\Steps\RunStep;
use WorldFactory\QQ\Foundations\AbstractStage;
use WorldFactory\QQ\Misc\StepWalker;

/**
 * Class RunStage
 * @package WorldFactory\QQ\Entities\Stages
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
