<?php

namespace WorldFactory\QQ\Entities\Stages;

use Exception;
use WorldFactory\QQ\Foundations\AbstractStage;
use WorldFactory\QQ\Entities\Steps\ArrayStep;
use WorldFactory\QQ\Misc\StepWalker;

/**
 * Class ArrayStage
 * @package WorldFactory\QQ\Entities\Stages
 *
 * @method ArrayStep getStep()
 */
class ArrayStage extends AbstractStage
{
    /**
     * @inheritdoc
     * @throws Exception
     */
    public function execute(StepWalker $stepWalker)
    {
        $result = [];

        foreach($this->getStep()->getChildren() as $step) {
            $stepResult = $stepWalker->walk($step);

            if(is_array($stepResult)) {
                $result = array_merge($result, $stepResult);
            } elseif (strpos($stepResult, PHP_EOL) !== false) {
                $array = explode(PHP_EOL, $stepResult);

                foreach($array as $line) {
                    if (!empty($line)) {
                        $result[] = $line;
                    }
                }
            } else {
                $array = preg_split('/[\s]+/', trim($stepResult));

                foreach($array as $line) {
                    if (!empty($line)) {
                        $result[] = $line;
                    }
                }
            }
        }

        return $result;
    }
}
