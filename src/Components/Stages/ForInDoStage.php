<?php

namespace WorldFactory\QQ\Components\Stages;

use Exception;
use Symfony\Component\Console\Output\OutputInterface;
use WorldFactory\QQ\Components\Context;
use WorldFactory\QQ\Components\Steps\ForInDoStep;
use WorldFactory\QQ\Foundations\AbstractStage;
use WorldFactory\QQ\Foundations\AbstractStep;
use WorldFactory\QQ\Misc\StepWalker;

/**
 * Class ForInDoStage
 * @package WorldFactory\QQ\Components\Stages
 *
 * @method ForInDoStep getStep()
 */
class ForInDoStage extends AbstractStage
{
    /** @var OutputInterface */
    private $output;

    /** @var Context */
    private $context;

    /**
     * ForInDoStage constructor.
     * @param AbstractStep $step
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
        $var = $this->getStep()->getFor();
        $in = $this->getStep()->getIn();
        $do = $this->getStep()->getDo();

        $source = $this->getStep()->getSource();

        if (!is_string($source)) {
            $source = "Composite source";
        }

        $this->output->writeln("-> Set <fg=white;bg=cyan>$var</> for each <fg=white;bg=cyan>$source</>");

        $array = $stepWalker->walk($in);

        if (is_string($array)) {
            if (strpos($array, PHP_EOL) === false) {
                $array = preg_split("/[ \t]+/", $array, 0, PREG_SPLIT_NO_EMPTY);
            } else {
                $array = preg_split("/[\n\r]+/", $array, 0, PREG_SPLIT_NO_EMPTY);
            }
        }

        $result = null;

        foreach ($array as $value) {
            $this->context->set($var, $value);

            $result = $stepWalker->walk($do);
        }

        return $result;
    }
}
