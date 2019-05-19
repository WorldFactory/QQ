<?php

namespace WorldFactory\QQ\Services\Runners;

use Exception;
use WorldFactory\QQ\Foundations\AbstractRunner;
use WorldFactory\QQ\Misc\TemporizedExecution;

class PHPRunner extends AbstractRunner
{
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
        $this->result = null;
        $this->script = $script;

        $execution = new TemporizedExecution($this->getOutput(), [$this, 'executeTemporized']);

        $execution->execute();

        $this->getOutput()->writeln('');

        return $this->result;
    }

    public function executeTemporized()
    {
        $_parameters = $this->getContext()->getParameters();

        extract($_parameters);

        $this->result = eval($this->script);
    }
}