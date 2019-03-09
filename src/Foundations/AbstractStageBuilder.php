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
    abstract public function build(AbstractStep $step, Context $context) : AbstractStage;
}
