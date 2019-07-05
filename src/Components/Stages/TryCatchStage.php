<?php

namespace WorldFactory\QQ\Components\Stages;

use Exception;
use Symfony\Component\Console\Output\OutputInterface;
use WorldFactory\QQ\Components\Steps\TryCatchStep;
use WorldFactory\QQ\Foundations\AbstractStage;
use WorldFactory\QQ\Foundations\AbstractStep;
use WorldFactory\QQ\Misc\StepWalker;

/**
 * Class TryCatchStage
 * @package WorldFactory\QQ\Components\Stages
 *
 * @method TryCatchStep getStep()
 */
class TryCatchStage extends AbstractStage
{
    /** @var OutputInterface */
    private $output;

    /**
     * TryCatchStage constructor.
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
        $try = $this->getStep()->getTry();
        $catch = $this->getStep()->getCatch();

        $this->output->writeln("-> <fg=white;bg=cyan>Try command :</>");

        try {
            $result = $stepWalker->walk($try);
        } catch (Exception $exception) {
            $this->output->writeln("<error>{$exception->getMessage()}</>");

            if ($catch !== null) {
                $result = $stepWalker->walk($catch);
            } else {
                $result = false;
            }
        }

        return $result;
    }
}
