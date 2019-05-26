<?php

namespace WorldFactory\QQ\Services\Runners;

use Exception;
use WorldFactory\QQ\Misc\TemporizedExecution;

class ExpressionRunner extends PHPRunner
{
    protected const SHORT_DESCRIPTION = "Execute PHP expression with 'eval' function and return it's result.";

    protected const LONG_DESCRIPTION = <<<EOT
This Runner allows you to execute PHP code with the 'eval' function.
Before it runs, your code is encapsulated as follows: "return (<your_code>);"
The context is the 'execute' method of the ExpressionRunner class.
You have at your disposal all parameters extracted in the current symbol table, as well as all the protected and private methods of the ExpressionRunner.
EOT;

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function execute(string $script)
    {
        $script = "return ($script);";

        $_parameters = $this->getContext()->getParameters();

        extract($_parameters);

        $result = eval($script);

        return $result;
    }
}