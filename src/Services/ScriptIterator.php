<?php

namespace WorldFactory\QQ\Services;

use Exception;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use WorldFactory\QQ\Entities\Script;
use WorldFactory\QQ\Interfaces\RunnerInterface;
use WorldFactory\QQ\Interfaces\ScriptFormatterInterface;
use WorldFactory\QQ\Interfaces\TokenizedInputInterface;
use WorldFactory\QQ\Misc\BasicCommand;

class ScriptIterator
{
    /** @var ContainerInterface */
    private $container;

    /** @var RunnerFactory */
    private $runnerFactory;

    /** @var TokenizedInputInterface */
    private $input;

    /** @var OutputInterface */
    private $output;

    /** @var BasicCommand */
    private $command;

    public function __construct(ContainerInterface $container, RunnerFactory $runnerFactory)
    {
        $this->container = $container;
        $this->runnerFactory = $runnerFactory;
    }

    public function setInputOutput(TokenizedInputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        $this->runnerFactory->setInputOutput($input, $output);
    }

    /**
     * @param $command
     * @deprecated 1.5.0
     */
    public function setCommand($command)
    {
        $this->command = $command;
    }

    /**
     * @param Script $script
     * @return \Generator
     * @throws Exception
     */
    public function browse(Script $script)
    {
        $this->compileScript($script);

        if ($script->hasChildren()) {
            /** @var Script $child */
            foreach ($script->getChildren() as $child) {
                /** @var Script|null $subChild */
                foreach($this->browse($child) as $subChild) {
                    if ($subChild) {
                        yield $subChild;
                    }
                }
            }
        } elseif ($script->isExecutable()) {
            yield $script;
        }
    }

    /**
     * @param Script $script
     * @throws Exception
     */
    protected function compileScript(Script $script)
    {
        /** @var ScriptFormatterInterface $formatter */
        $formatter = $this->container->get('qq.formatter.script');

        $formatter->setTokens($script->getTokens());

        /** @var RunnerInterface */
        $runner = $this->findRunner($script)
            ->setCommand($this->command, true)
            ->setVarFormatter($formatter)
        ;

        $script->setFormatter($formatter);
        $script->setRunner($runner);

        $script->compile();
    }

    /**
     * @param string $script
     * @return RunnerInterface
     * @throws Exception
     */
    protected function findRunner(Script $script) : RunnerInterface
    {
        /** @var string $type */
        $type = $script->getType();

        if (preg_match(RunnerFactory::PROTOCOL_REGEX, $script->getScript(), $result)) {
            $type = $result['type'];
        }

        /** @var RunnerInterface $runner */
        $runner = $this->runnerFactory->getRunner($type);

        return $runner;
    }

}