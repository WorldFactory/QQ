<?php

namespace WorldFactory\QQ\Misc;

use Exception;
use function get_class;
use WorldFactory\QQ\Entities\Context;
use WorldFactory\QQ\Entities\RunnerConfig;
use WorldFactory\QQ\Foundations\AbstractStep;
use WorldFactory\QQ\Interfaces\TokenizedInputInterface;
use WorldFactory\QQ\Services\DeprecationHandler;
use function preg_match;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use WorldFactory\QQ\Services\StageFactory;
use WorldFactory\QQ\Services\StepFactory;

class BasicCommand extends Command implements ContainerAwareInterface
{
    /** @var array Configuration de la commande. */
    private $config = [];

    /** @var TokenizedInputInterface */
    private $input;

    /** @var OutputInterface */
    private $output;

    /** @var bool */
    private $displayHeader = true;

    /** @var ContainerInterface */
    private $container;

    /**
     * BasicCommand constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;

        parent::__construct($this->config['name']);
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @return OutputInterface
     */
    public function getOutput(): OutputInterface
    {
        return $this->output;
    }

    /**
     * @return TokenizedInputInterface
     */
    public function getInput(): TokenizedInputInterface
    {
        return $this->input;
    }

    protected function configure()
    {
        $this
            ->setName($this->config['name'])
            ->setDescription($this->config['shortDescription'] ?? null)
            ->setHelp($this->config['longDescription'] ?? null)
            ->setAliases($this->config['aliases'] ?? [])
            ->ignoreValidationErrors()
        ;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function setDisplayHeader (bool $displayHeader)
    {
        $this->displayHeader = $displayHeader;

        return $this;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws Exception
     * @throws \Throwable
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$input instanceof TokenizedInputInterface) {
            throw new \BadMethodCallException("BasicCommand::execute only support TokenizedInputInterface.");
        }

        $this->input = $input;
        $this->output = $output;

        $root = $this->buildStepTree();
        $context = $this->buildContext();
        $stepWalker = $this->buildStepWalker($context);

        if ($this->displayHeader) {
            $this->writeHeader();
        }

        $stepWalker->walk($root);

        if ($this->displayHeader) {
            $this->writeFooter();
        }

        return 0;
    }

    /**
     * @return AbstractStep
     * @throws Exception
     */
    protected function buildStepTree() : AbstractStep
    {
        /** @var StepFactory $stepFactory */
        $stepFactory = $this->container->get('qq.factory.step');

        return $stepFactory->buildStep($this->config, new RunnerConfig(['type' => $this->config['type'] ?? 'shell']));
    }

    protected function buildContext() : Context
    {
        /** @var ConfigLoader $loaderConfig */
        $loaderConfig = $this->container->get('qq.loader.config');

        return new Context(
            $loaderConfig->getParameters(),
            $this->input->getArgumentTokens(),
            $this->input,
            $this->output
        );
    }

    /**
     * @return StepWalker
     */
    protected function buildStepWalker(Context $context) : StepWalker
    {
        /** @var StageFactory $stageFactory */
        $stageFactory = $this->container->get('qq.factory.stage');

        return new StepWalker($stageFactory, $context);
    }

    protected function writeHeader()
    {
        $formatter = $this->getHelper('formatter');

        $this->output->writeln($formatter->formatSection(
            'Task name  ',
            $this->getName()
        ));

        if ($this->getDescription()) {
            $this->output->writeln($formatter->formatSection(
                'Description',
                $this->getDescription()
            ));
        }
    }

    protected function writeFooter()
    {
        /** @var DeprecationHandler $deprecationHandler */
        $deprecationHandler = $this->container->get('qq.handler.deprecation');

        if (count($deprecationHandler->getDeprecations()) > 0) {
            $this->output->writeln("<error>Several deprecation messages were generated. Remember to change your code to make it easier for you to upgrade to the higher version of QQ.</error>");
            foreach ($deprecationHandler->getDeprecations() as $deprecation) {
                $this->output->writeln("* $deprecation");
            }
        }
    }
}
