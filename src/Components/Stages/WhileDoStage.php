<?php

namespace WorldFactory\QQ\Components\Stages;

use Exception;
use Symfony\Component\Console\Output\OutputInterface;
use WorldFactory\QQ\Components\Steps\WhileDoStep;
use WorldFactory\QQ\Foundations\AbstractStage;
use WorldFactory\QQ\Foundations\AbstractStep;
use WorldFactory\QQ\Misc\StepWalker;

/**
 * Class WhileDoStage
 * @package WorldFactory\QQ\Components\Stages
 *
 * @method WhileDoStep getStep()
 */
class WhileDoStage extends AbstractStage
{
    /** @var OutputInterface */
    private $output;

    /**
     * WhileDoStage constructor.
     * @param AbstractStep $step
     * @param OutputInterface $output
     */
    public function __construct(AbstractStep $step, OutputInterface $output)
    {
        parent::__construct($step);

        $this->output = $output;
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function execute(StepWalker $stepWalker)
    {
        $while = $this->getStep()->getWhile();
        $do = $this->getStep()->getDo();

        $condition = $this->getStep()->getCondition();

        if (!is_string($condition)) {
            $condition = "Composite test";
        }

        $this->output->writeln("-> While test : <fg=white;bg=cyan>$condition</>");

        $test = $stepWalker->walk($while);

        $result = null;

        while ($test === true) {
            $result = $stepWalker->walk($do);

            $test = $stepWalker->walk($while);
        }

        return $result;
    }
}
