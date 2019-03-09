<?php

namespace WorldFactory\QQ\Services;

use Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;
use WorldFactory\QQ\Entities\Context;
use WorldFactory\QQ\Foundations\AbstractStage;
use WorldFactory\QQ\Foundations\AbstractStageBuilder;
use WorldFactory\QQ\Foundations\AbstractStep;

class StageFactory
{
    /** @var ContainerInterface */
    private $container;

    private $stageBuilders = [];

    /**
     * StageFactory constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $name
     * @param string $id
     */
    public function addStageBuilder(string $name, string $id)
    {
        $this->stageBuilders[$name] = $this->container->get($id);
    }

    /**
     * @param AbstractStep $step
     * @param Context $context
     * @return AbstractStage
     * @throws Exception
     */
    public function buildStage(AbstractStep $step, Context $context) : AbstractStage
    {
        /** @var AbstractStageBuilder $stageBuilder */
        $stageBuilder = $this->getStageBuilder($step);

        return $stageBuilder->build($step, $context);
    }

    /**
     * @param AbstractStep $step
     * @return AbstractStageBuilder
     * @throws Exception
     */
    protected function getStageBuilder(AbstractStep $step) : AbstractStageBuilder
    {
        /** @var AbstractStageBuilder $stageBuilder */
        foreach ($this->stageBuilders as $stageBuilder) {
            if ($stageBuilder->isValid($step)) {
                return $stageBuilder;
            }
        }

        $stepName = get_class($step);

        throw new Exception("Unable to fin stage builder for this step : $stepName.");
    }
}
