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
        $test = $this->accreditor->test();

        $then = $this->getStep()->getThen();
        $else = $this->getStep()->getElse();

        if ($test) {
            $stepWalker->walk($then);
        } elseif ($else !== null) {
            $stepWalker->walk($else);
        }

        return true;
    }
}
