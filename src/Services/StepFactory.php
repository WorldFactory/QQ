<?php

namespace WorldFactory\QQ\Services;

use Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;
use WorldFactory\QQ\Entities\RunnerConfig;
use WorldFactory\QQ\Foundations\AbstractStep;
use WorldFactory\QQ\Services\StepBuilders\AbstractStepBuilder;

class StepFactory
{
    /** @var AbstractStepBuilder[]  */
    private $stepBuilders = [];

    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function addStepBuilder(string $name, string $id)
    {
        /** @var AbstractStepBuilder $stepBuilder */
        $stepBuilder = $this->container->get($id);

        $stepBuilder->setStepFactory($this);

        $this->stepBuilders[$name] = $stepBuilder;
    }

    public function buildStep($definition, RunnerConfig $runnerConfig) :? AbstractStep
    {
        /** @var AbstractStep|null $step */
        $step = null;

        /** @var AbstractStepBuilder $stepBuilder */
        foreach ($this->stepBuilders as $stepBuilder) {
            if ($stepBuilder->isValid($definition)) {
                $context = (is_array($definition) && array_key_exists('options', $definition))
                    ? $runnerConfig->merge($definition['options'])
                    : $runnerConfig->clone()
                ;
                $step = $stepBuilder->build($definition, $context);
                break;
            }
        }

        if ($step === null) {
            if (is_string($definition)) {
                $def = "'" . substr($definition, 0, 50) . "...'";
            } elseif (is_array($definition)) {
                $def = "[" . join(', ', array_keys($definition)) . "]";
            } else {
                $def = "Unknown type";
            }

            throw new Exception("Unable to fin step builder for definition : $def.");
        }

        return $step;
    }
}