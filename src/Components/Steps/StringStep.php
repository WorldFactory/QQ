<?php

namespace WorldFactory\QQ\Components\Steps;

use WorldFactory\QQ\Misc\OptionBag;
use WorldFactory\QQ\Foundations\AbstractStep;
use WorldFactory\QQ\Services\StepFactory;

class StringStep extends AbstractStep
{
    protected $script;

    /**
     * StringStep constructor.
     * @param StepFactory $stepFactory
     * @param OptionBag $config
     * @param string $script
     */
    public function __construct(StepFactory $stepFactory, OptionBag $config, string $script)
    {
        parent::__construct($stepFactory, $config);

        $this->script = $script;
    }

    /**
     * @return string
     */
    public function getScript(): string
    {
        return $this->script;
    }
}