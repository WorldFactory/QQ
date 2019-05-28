<?php

namespace WorldFactory\QQ\Entities\Stages;

use Exception;
use Symfony\Component\Console\Output\OutputInterface;
use WorldFactory\QQ\Entities\Steps\IfThenElseStep;
use WorldFactory\QQ\Foundations\AbstractStage;
use WorldFactory\QQ\Foundations\AbstractStep;
use WorldFactory\QQ\Misc\StepWalker;

/**
 * Class IfThenElseStage
 * @package WorldFactory\QQ\Entities\Stages
 *
 * @method IfThenElseStep getStep()
 */
class IfThenElseStage extends AbstractStage
{
    /** @var OutputInterface */
    private $output;

    /**
     * IfThenElseStage constructor.
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
        $if = $this->getStep()->getIf();
        $then = $this->getStep()->getThen();
        $else = $this->getStep()->getElse();

        $this->output->writeln("-> Running test : ");

        $test = (bool) $stepWalker->walk($if);

        $result = null;

        if ($test) {
            $this->output->writeln("-> Result : <fg=white;bg=green>TRUE</>");
            $result = $stepWalker->walk($then);
        } else {
            $this->output->writeln("-> Result : <fg=white;bg=red>FALSE</>");

            if ($else !== null) {
                $result = $stepWalker->walk($else);
            }
        }

        return $result;
    }
}
