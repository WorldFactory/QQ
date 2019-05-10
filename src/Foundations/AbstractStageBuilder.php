<?php

namespace WorldFactory\QQ\Foundations;

use WorldFactory\QQ\Entities\Context;

abstract class AbstractStageBuilder
{
    /**
     * @param AbstractStep $step
     * @return bool
     */
    abstract public function isValid(AbstractStep $step) : bool;

    /**
     * @param AbstractStep $step
     * @param Context $context
     * @return AbstractStage
     */
    public function build(AbstractStep $step, Context $context): AbstractStage
    {
        if ($this->isValid($step)) {
            return $this->buildStage($step, $context);
        } else {
            throw new \LogicException("Unrecognized step : " . get_class($step));
        }
    }

    /**
     * @param AbstractStep $step
     * @param Context $context
     * @return AbstractStage
     */
    abstract protected function buildStage($step, Context $context) : AbstractStage;
}
