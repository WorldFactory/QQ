<?php

namespace WorldFactory\QQ\Entities\Stages;

use WorldFactory\QQ\Entities\Steps\LeafStep;
use WorldFactory\QQ\Foundations\AbstractStage;
use WorldFactory\QQ\Foundations\AbstractStep;
use WorldFactory\QQ\Interfaces\RunnerInterface;
use WorldFactory\QQ\Misc\StepWalker;

/**
 * Class ArrayStage
 * @package WorldFactory\QQ\Entities\Stages
 *
 * @method LeafStep getStep()
 */
class LeafStage extends AbstractStage
{
    /** @var string Final script to execute */
    private $compiledScript;

    /** @var RunnerInterface Runner to be use to run script */
    private $runner;

    /**
     * LeafStage constructor.
     * @param AbstractStep $step
     * @param string $compiledScript
     * @param RunnerInterface $runner
     */
    public function __construct(AbstractStep $step, string $compiledScript, RunnerInterface $runner)
    {
        parent::__construct($step);

        $this->compiledScript = $compiledScript;
        $this->runner = $runner;
    }

    /**
     * @inheritdoc
     */
    public function execute(StepWalker $stepWalker) : bool
    {
        $this->runner->run($this->compiledScript);

        return true;
    }
}
