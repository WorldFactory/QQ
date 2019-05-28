<?php

namespace WorldFactory\QQ\Entities\Steps;

use Exception;
use WorldFactory\QQ\Misc\OptionBag;
use WorldFactory\QQ\Foundations\AbstractStep;
use WorldFactory\QQ\Services\StepFactory;

class RunStep extends AbstractStep
{
    protected $child;

    /**
     * RunStep constructor.
     * @param StepFactory $stepFactory
     * @param OptionBag $config
     * @param array $definition
     * @throws Exception
     */
    public function __construct(StepFactory $stepFactory, OptionBag $config, array $definition)
    {
        parent::__construct($stepFactory, $config);

        if (array_key_exists('run', $definition)) {
            $child = $definition['run'];
        } elseif (array_key_exists('script', $definition)) {
            $child = $definition['script'];

            trigger_error("In your 'command.yml' file, 'script' key is deprecated. Consider to use 'run' key instead.", E_USER_DEPRECATED);
        }


        $this->child = $stepFactory->buildStep($child, $this->getOptionBag());
    }

    /**
     * @return AbstractStep|null
     */
    public function getChild() : AbstractStep
    {
        return $this->child;
    }
}