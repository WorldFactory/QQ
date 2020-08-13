<?php

namespace WorldFactory\QQ\Services\Runners;

use Exception;
use WorldFactory\QQ\Foundations\AbstractRunner;
use WorldFactory\QQ\Interfaces\RunnerInterface;
use WorldFactory\QQ\Misc\RunnerOptionBag;
use WorldFactory\QQ\Services\RunnerFactory;

class DockerRunner extends AbstractRunner
{
    protected const OPTION_DEFINITIONS = [
        'target'     => [
            'type' => 'string',
            'required' => true,
            'description' => "The targeted container."
        ],
        'user'       => [
            'type' => 'string',
            'description' => "The user with whom the script is to be executed."
        ],
        'env'        => [
            'type' => 'array',
            'description' => "Environment variables that must be injected.",
            'default' => []
        ],
        'workingDir' => [
            'type' => 'string',
            'description' => "The internal working directory that should be used."
        ],
        'flags'      => [
            'type' => 'array',
            'description' => "The flags to activate when running the script.",
            'default' => []
        ],
        'tty'      => [
            'type' => 'bool',
            'description' => "Use TTY to launch script.",
            'default' => true
        ],
        'subtty'      => [
            'type' => 'bool',
            'description' => "Use TTY in Shell sub-runner.",
            'default' => false
        ],
        'wrap'      => [
            'type' => 'bool',
            'description' => "Wrap script into interpreter.",
            'default' => false
        ],
        'wrapper'      => [
            'type' => 'string',
            'description' => "Define interpreter wrapper. Only Bash, Sh and PHP are recognized a this time.",
            'default' => 'sh'
        ],
        'trim' => [
            'type' => 'bool',
            'description' => "Trim result if it's a string.",
            'default' => true
        ]
    ];

    protected const SHORT_DESCRIPTION = "Run script in target Docker container. ";

    protected const LONG_DESCRIPTION = <<<EOT
The specified script is executed directly in the targeted container.
You can specify a particular user or internal directory.
You can also inject environment variables when running the script.
EOT;

    /** @var RunnerFactory */
    private $runnerFactory;

    /**
     * @param RunnerFactory $runnerFactory
     */
    public function setRunnerFactory(RunnerFactory $runnerFactory) : void
    {
        $this->runnerFactory = $runnerFactory;
    }

    public function format(string $compiledScript) : string
    {
        $options = $this->getOptions();

        if (!isset($options['target'])) {
            throw new \InvalidArgumentException("You should define target container with 'target' parameter.");
        }

        /** @var string $target */
        $target = $options['target'];

        /** @var array $parameters */
        $parameters = [];

        if (isset($options['user'])) {
            $parameters[] = "--user=" . $options['user'];
        }

        if (!empty($options['env'])) {
            foreach ($options['env'] as $key => $val) {
                $parameters[] = "--env $key=\"$val\"";
            }
        }

        if (isset($options['workingDir'])) {
            $parameters[] = "--workdir=" . $options['workingDir'];
        }

        if (isset($options['flags']) and is_array($options['flags'])) {
            if (in_array('detach', $options['flags'])) {
                $parameters[] = "--detach";
            }

            if (in_array('interactive', $options['flags'])) {
                $parameters[] = "--interactive";
            }

            if (in_array('privileged', $options['flags'])) {
                $parameters[] = "--privileged";
            }
        }

        if ($options['tty']) {
            $parameters[] = "--tty";
        }

        if ($options['wrap']) {
            switch (strtolower($options['wrapper'])) {
                case 'sh':
                    $compiledScript = addcslashes($compiledScript, '\'');
                    $compiledScript = "sh -c '$compiledScript'";
                    break;
                case 'bash':
                    $compiledScript = addcslashes($compiledScript, '\'');
                    $compiledScript = "bash -c '$compiledScript'";
                    break;
                case 'php':
                    $compiledScript = addcslashes($compiledScript, '"');
                    $compiledScript = "php -r \"$compiledScript\"";
                    break;
                default:
                    throw new \RuntimeException("Unknown wrapper type : '{$options['wrapper']}'.");
            }
        }

        $execArgs = join(' ', $parameters);

        $targetContainer = $this->getTargetContainer($target);

        return "docker exec $execArgs $targetContainer $compiledScript";
    }

    /**
     * @param string $script
     * @throws Exception
     */
    public function execute(string $script)
    {
        $options = $this->getOptions();

        /** @var RunnerInterface $runner */
        $runner = null;

        $runner = $this->runnerFactory->getRunner('shell');

        $runnerConfig = new RunnerOptionBag(['tty' => $options['subtty'], 'trim' => $options['trim']]);

        $runnerConfig->link($runner);

        $runner
            ->setHeaderDisplayed(false)
            ->setInput($this->getInput())
            ->setOutput($this->getOutput())
        ;

        return $runner->run($script, $this->getContext());
    }

    protected function getTargetContainer($target)
    {
        if ($this->isUnix()) {
            $targetContainer = "`docker-compose ps -q $target`";
        } else {
            $targetContainer = trim(shell_exec("docker-compose ps -q $target"));
        }

        return $targetContainer;
    }

    /**
     * @return bool
     */
    protected function isUnix()
    {
        return PATH_SEPARATOR === ':';
    }
}