<?php

namespace WorldFactory\QQ\Misc;

use Exception;
use function get_class;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Helper\Table;
use WorldFactory\QQ\Entities\Context;
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

    /** @var mixed */
    private $result;

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
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
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

        $this->result = $stepWalker->walk($root);

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

        $rawOptions = [
            'type' => $this->config['type'] ?? 'shell'
        ];

        if (array_key_exists('options', $this->config)) {
            $rawOptions['options'] = $this->config['options'];
        }

        $options = new OptionBag($rawOptions);

        $options->addOptionDefinitions([
            'type'     => [
                'type' => 'string',
                'required' => true,
                'description' => "The default type to define which runner to be used."
            ],
            'options'     => [
                'type' => 'array',
                'required' => false,
                'description' => "List of the Runner options."
            ]
        ]);

        return $stepFactory->buildStep($this->config, $options);
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
            $formatter = new FormatterHelper();

            $message = [
                "/!\\ Several deprecation messages were generated. /!\\",
                "Remember to change your code to make it easier for you to upgrade to the higher version of QQ."
            ];

            $this->output->write(PHP_EOL);

            $this->output->writeln($formatter->formatBlock($message, 'fg=black;bg=yellow', TRUE));

            $deprecations = $deprecationHandler->getDeprecations();

            array_walk($deprecations, function(&$deprecation) {
                $deprecation = [$deprecation];
            });

            $table = new Table($this->output);
            $table
                ->setHeaders(['QQ deprecation list'])
                ->setRows($deprecations)
            ;
            $table->render();

            $this->output->write(PHP_EOL);
        }
    }
}
