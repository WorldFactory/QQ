<?php

namespace WorldFactory\QQ\Services\Runners;

use Exception;
use WorldFactory\QQ\Foundations\AbstractRunner;
use WorldFactory\QQ\Misc\TemporizedExecution;

class IncludeRunner extends AbstractRunner
{
    protected const SHORT_DESCRIPTION = "Include target PHP file.";

    protected const LONG_DESCRIPTION = <<<EOT
The script to run must be a valid PHP file.
This is included with the 'require' function.
The context is the 'executeTemporized' method of the IncludeRunner class.
You have at your disposal all parameters extracted in the current symbol table, as well as all the protected and private methods of the IncludeRunner.
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

        $this->result = require($this->script);
    }
}