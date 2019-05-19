<?php

namespace WorldFactory\QQ\Entities\Stages;

use Exception;
use Symfony\Component\Console\Output\OutputInterface;
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

    /** @var OutputInterface */
    private $output;

    /**
     * ConditionStage constructor.
     * @param AbstractStep $step
     * @param Accreditor $accreditor
     */
    public function __construct(AbstractStep $step, Accreditor $accreditor, OutputInterface $output)
    {
        parent::__construct($step);

        $this->accreditor = $accreditor;
        $this->output = $output;
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function execute(StepWalker $stepWalker)
    {
        $test = $this->accreditor->test();

        $then = $this->getStep()->getThen();
        $else = $this->getStep()->getElse();

        $result = null;

        $this->output->write("-> <fg=black;bg=cyan>{$this->accreditor->getCompiledCondition()}</> : ");

        if ($test) {
            $this->output->writeln("<fg=white;bg=green>TRUE</>");
            $result = $stepWalker->walk($then);
        } else {
            $this->output->writeln("<fg=white;bg=red>FALSE</>");

            if ($else !== null) {
                $result = $stepWalker->walk($else);
            }
        }

        return $result;
    }
}
