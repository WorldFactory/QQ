<?php

namespace WorldFactory\QQ\Entities\Stages;

use Exception;
use Symfony\Component\Console\Output\OutputInterface;
use WorldFactory\QQ\Entities\Context;
use WorldFactory\QQ\Entities\Steps\SetFromStep;
use WorldFactory\QQ\Foundations\AbstractStage;
use WorldFactory\QQ\Foundations\AbstractStep;
use WorldFactory\QQ\Misc\StepWalker;

/**
 * Class SetFromStage
 * @package WorldFactory\QQ\Entities\Stages
 *
 * @method SetFromStep getStep()
 */
class SetFromStage extends AbstractStage
{
    /** @var OutputInterface */
    private $output;

    /** @var Context */
    private $context;

    /**
     * SetFromStage constructor.
     * @param AbstractStep $step
     * @param Context $context
     * @param OutputInterface $output
     */
    public function __construct(AbstractStep $step, Context $context, OutputInterface $output)
    {
        parent::__construct($step);

        $this->output = $output;
        $this->context = $context;
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function execute(StepWalker $stepWalker)
    {

        $set = $this->getStep()->getSet();
        $from = $this->getStep()->getFrom();

        $result = null;

        $this->output->writeln("-> <fg=black;bg=cyan>Setting '$set' parameter.</>");

        $result = $stepWalker->walk($from);

        $this->context->set($set, $result);

        return null;
    }
}
