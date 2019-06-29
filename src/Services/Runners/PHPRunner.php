<?php

namespace WorldFactory\QQ\Services\Runners;

use Exception;
use WorldFactory\QQ\Foundations\AbstractRunner;
use WorldFactory\QQ\Misc\TemporizedExecution;

class PHPRunner extends AbstractRunner
{
    protected const OPTION_DEFINITIONS = [
        'eol' => [
            'type' => 'bool',
            'description' => "Write EOL at end of script running.",
            'default' => false
        ],
        'trim' => [
            'type' => 'bool',
            'description' => "Trim result if it's a string.",
            'default' => true
        ]
    ];

    protected const SHORT_DESCRIPTION = "Execute PHP code with 'eval' function.";

    protected const LONG_DESCRIPTION = <<<EOT
This Runner allows you to execute PHP code with the 'eval' function.
The context is the 'executeTemporized' method of the PHPRunner class.
You have at your disposal all parameters extracted in the current symbol table, as well as all the protected and private methods of the PHPRunner.
EOT;

    /** @var string */
    private $script;

    /** @var mixed */
    private $result;

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function execute(string $script)
    {
        $options = $this->getOptions();

        $this->result = null;
        $this->script = $script;

        $execution = new TemporizedExecution($this->getOutput(), [$this, 'executeTemporized']);

        $execution->execute();

        if ($options['eol']) {
            $this->getOutput()->write(PHP_EOL);
        }

        return (is_string($this->result) && $options['trim']) ? trim($this->result) : $this->result;
    }

    public function executeTemporized()
    {
        $_parameters = $this->getContext()->getParameters();

        extract($_parameters);

        $this->result = eval($this->script);
    }
}