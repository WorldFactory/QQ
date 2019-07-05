<?php

namespace WorldFactory\QQ\Components\Stages;

use Exception;
use WorldFactory\QQ\Components\Context;
use WorldFactory\QQ\Components\Steps\StringStep;
use WorldFactory\QQ\Foundations\AbstractStage;
use WorldFactory\QQ\Foundations\AbstractStep;
use WorldFactory\QQ\Interfaces\RunnerInterface;
use WorldFactory\QQ\Misc\StepWalker;

/**
 * Class StringStage
 * @package WorldFactory\QQ\Components\Stages
 *
 * @method StringStep getStep()
 */
class StringStage extends AbstractStage
{
    /** @var string Final script to execute */
    private $compiledScript;

    /** @var RunnerInterface Runner to be use to run script */
    private $runner;

    /** @var Context */
    private $context;

    /**
     * StringStage constructor.
     * @param AbstractStep $step
     * @param string $compiledScript
     * @param RunnerInterface $runner
     */
    public function __construct(AbstractStep $step, string $compiledScript, RunnerInterface $runner, Context $context)
    {
        parent::__construct($step);

        $this->compiledScript = $compiledScript;
        $this->runner = $runner;
        $this->context = $context;
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function execute(StepWalker $stepWalker)
    {
        return $this->runner->run($this->compiledScript, $this->context);
    }
}
