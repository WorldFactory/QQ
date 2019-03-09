<?php

namespace WorldFactory\QQ\Services\StageBuilders;

use WorldFactory\QQ\Entities\Context;
use WorldFactory\QQ\Entities\RunnerConfig;
use WorldFactory\QQ\Entities\Script;
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

    public function build(AbstractStep $step, Context $context): AbstractStage
    {
        $formatter = new ContextualizedFormatter($context);

        $runner = $this->findRunner($step, $context);

        // @todo à virer après refacto du DockerRunner
        $runner->setVarFormatter($formatter);

        $options = $step->getRunnerConfig();
        $options->setOptionDefinitions($runner->getOptionDefinitions());

        $options->compile($formatter);

        $compiledScript = $step->getScript();

        $compiledScript = $formatter->sanitize($compiledScript);
        $compiledScript = $formatter->format($compiledScript);

        $compiledScript = $runner->format($this, $compiledScript);

        $compiledScript = $formatter->finalize($compiledScript);

        return new LeafStage($step, $compiledScript, $runner);
    }

    /**
     * @param string $script
     * @return RunnerInterface
     * @throws \Exception
     */
    protected function findRunner(LeafStep $leafStep, Context $context) : RunnerInterface
    {
        /** @var RunnerConfig $config */
        $config = $leafStep->getRunnerConfig();

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

        return $runner;
    }

    /**
     * @param Script $script
     * @throws \Exception
     */
    public function buildStage(AbstractStep $stage, Context $context)
    {
        /** @var ScriptFormatterInterface $formatter */
        $formatter = $this->container->get('qq.formatter.script');

        $formatter->setTokens($script->getTokens());

        /** @var RunnerInterface */
        $runner = $this->findRunner($script)
            ->setVarFormatter($formatter)
            ->setInput($this->input)
            ->setOutput($this->output)
        ;

        $script->setFormatter($formatter);
        $script->setRunner($runner);

        $script->compile();

        return $stage;
    }

    public function compile() : void
    {
        if ($this->compiledScript !== null) {
            throw new \LogicException(("Script already compiled."));
        }

        $this->options->setOptionDefinitions($this->runner->getOptionDefinitions());

        $this->options->compile($this->formatter);

        $this->compiledScript = $this->compileScript();
    }

    protected function compileScript() :? string
    {
        if ($this->isConditionnal()) {
            $this->accreditor->compile($this->formatter);
        }

        $compiledScript = $this->getScript();

        if (!empty($compiledScript)) {
            $compiledScript = $this->formatter->sanitize($compiledScript);
            $compiledScript = $this->formatter->format($compiledScript);

            $compiledScript = $this->runner->format($this, $compiledScript);

            $compiledScript = $this->formatter->finalize($compiledScript);
        }

        return $compiledScript;
    }
}
