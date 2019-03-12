<?php

namespace WorldFactory\QQ\Services\StageBuilders;

use WorldFactory\QQ\Entities\Context;
use WorldFactory\QQ\Misc\OptionBag;
use WorldFactory\QQ\Misc\RunnerOptionBag;
use WorldFactory\QQ\Entities\Stages\LeafStage;
use WorldFactory\QQ\Entities\Steps\LeafStep;
use WorldFactory\QQ\Foundations\AbstractStage;
use WorldFactory\QQ\Foundations\AbstractStageBuilder;
use WorldFactory\QQ\Foundations\AbstractStep;
use WorldFactory\QQ\Interfaces\RunnerInterface;
use WorldFactory\QQ\Interfaces\ScriptFormatterInterface;
use WorldFactory\QQ\Misc\ContextualizedFormatter;
use WorldFactory\QQ\Services\RunnerFactory;

class LeafStageBuilder extends AbstractStageBuilder
{
    /** @var RunnerFactory */
    private $runnerFactory;

    public function __construct(RunnerFactory $runnerFactory)
    {
        $this->runnerFactory = $runnerFactory;
    }

    public function isValid(AbstractStep $step): bool
    {
        return $step instanceof LeafStep;
    }

    /**
     * @param LeafStep $step
     * @param Context $context
     * @return AbstractStage
     * @throws \Exception
     */
    public function build(AbstractStep $step, Context $context): AbstractStage
    {
        $formatter = new ContextualizedFormatter($context);

        $runner = $this->buildRunner($step, $context, $formatter);

        $compiledScript = $this->compileScript($step, $formatter, $runner);

        return new LeafStage($step, $compiledScript, $runner);
    }

    /**
     * @param string $script
     * @return RunnerInterface
     * @throws \Exception
     */
    protected function buildRunner(LeafStep $leafStep, Context $context, ScriptFormatterInterface $formatter) : RunnerInterface
    {
        /** @var OptionBag $config */
        $config = $leafStep->getOptionBag();

        /** @var string $type */
        $type = $config['type'];

        if (preg_match(RunnerFactory::PROTOCOL_REGEX, $leafStep->getScript(), $result)) {
            $type = $result['type'];
        }

        /** @var RunnerInterface $runner */
        $runner = $this->runnerFactory
            ->getRunner($type)
            ->setInput($context->getInput())
            ->setOutput($context->getOutput())
        ;

        $runnerConfig = new RunnerOptionBag(isset($config['runner']) ? $config['runner'] : []);

        $runnerConfig->link($runner);

        $runnerConfig->compile($formatter);

        return $runner;
    }

    protected function compileScript(LeafStep $step, ScriptFormatterInterface $formatter, RunnerInterface $runner) : string
    {
        $compiledScript = $step->getScript();

        $compiledScript = $formatter->sanitize($compiledScript);
        $compiledScript = $formatter->format($compiledScript);

        $compiledScript = $runner->format($compiledScript);

        $compiledScript = $formatter->finalize($compiledScript);

        return $compiledScript;
    }
}
