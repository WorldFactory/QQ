<?php

namespace WorldFactory\QQ\Services;

use Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;
use WorldFactory\QQ\Misc\OptionBag;
use WorldFactory\QQ\Foundations\AbstractStep;
use WorldFactory\QQ\Foundations\AbstractStepBuilder;

class StepFactory
{
    const INHERITED_OPTIONS = ['type', 'options'];

    /** @var AbstractStepBuilder[]  */
    private $stepBuilders = [];

    /** @var ContainerInterface */
    private $container;

    /**
     * StepFactory constructor.
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
    public function addStepBuilder(string $name, string $id)
    {
        /** @var AbstractStepBuilder $stepBuilder */
        $stepBuilder = $this->container->get($id);

        $stepBuilder->setStepFactory($this);

        $this->stepBuilders[$name] = $stepBuilder;
    }

    /**
     * @param $definition
     * @param OptionBag $config
     * @return AbstractStep|null
     * @throws Exception
     */
    public function buildStep($definition, OptionBag $config) :? AbstractStep
    {
        /** @var AbstractStep|null $step */
        $step = null;

        /** @var AbstractStepBuilder $stepBuilder */
        foreach ($this->stepBuilders as $stepBuilder) {
            if ($stepBuilder->isValid($definition)) {
                $extendedConfig = $this->extendsConfig($config, is_array($definition) ? $definition : []);
                $step = $stepBuilder->build($definition, $extendedConfig);
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

    /**
     * @param OptionBag $config
     * @param array $definition
     * @return OptionBag
     */
    protected function extendsConfig(OptionBag $config, array $definition)
    {
        $extendedConfig = [];

        foreach (self::INHERITED_OPTIONS as $name) {
            if (array_key_exists($name, $definition)) {
                $extendedConfig[$name] = $definition[$name];
            }
        }

        return $config->merge($extendedConfig);
    }
}