<?php

namespace WorldFactory\QQ\Entities\Stages;

use WorldFactory\QQ\Entities\Accreditor;
use WorldFactory\QQ\Entities\Steps\ConditionStep;
use WorldFactory\QQ\Foundations\AbstractStage;
use WorldFactory\QQ\Foundations\AbstractStep;
use WorldFactory\QQ\Misc\StepWalker;

/**
 * Class ConditionStage
 * @package WorldFactory\QQ\Entities\Stages
 *
 * @method ConditionStep getStep()
 */
class ConditionStage extends AbstractStage
{
    /** @var Accreditor Entity used to define if runner should execute 'then' statement, or 'else' statement */
    private $accreditor;

    /**
     * ConditionStage constructor.
     * @param AbstractStep $step
     * @param Accreditor $accreditor
     */
    public function __construct(AbstractStep $step, Accreditor $accreditor)
    {
        parent::__construct($step);

        $this->accreditor = $accreditor;
    }

    /**
     * @inheritdoc
     */
    public function execute(StepWalker $stepWalker) : bool
    {
        if ($this->accreditor->test()) {
            $stepWalker->walk($this->getStep()->getThen());
        } else {
            $stepWalker->walk($this->getStep()->getElse());
        }

        return true;
    }
}
