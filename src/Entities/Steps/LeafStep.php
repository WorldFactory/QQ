<?php

namespace WorldFactory\QQ\Entities\Steps;

use WorldFactory\QQ\Misc\OptionBag;
use WorldFactory\QQ\Foundations\AbstractStep;
use WorldFactory\QQ\Services\StepFactory;

class LeafStep extends AbstractStep
{
    protected $script;

    /**
     * LeafStep constructor.
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