<?php

namespace WorldFactory\QQ\Foundations;

use WorldFactory\QQ\Misc\StepWalker;

abstract class AbstractStage
{
    private $step;

    /**
     * AbstractStage constructor.
     * @param AbstractStep $step
     */
    public function __construct(AbstractStep $step)
    {
        $this->step = $step;
    }

    /**
     * @return AbstractStep
     */
    public function getStep(): AbstractStep
    {
        return $this->step;
    }

    /**
     * @param StepWalker $stepWalker
     * @return mixed
     */
    abstract public function execute(StepWalker $stepWalker);
}
