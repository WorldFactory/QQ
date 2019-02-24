<?php

namespace WorldFactory\QQ\Entities;

use ParseError;
use RuntimeException;
use WorldFactory\QQ\Interfaces\ScriptFormatterInterface;

class Accreditor
{
    /** @var bool|null If script is executable */
    private $executable = null;

    /** @var string|null Condition to define if script is executable */
    private $condition;

    /** @var string|null The compiled condition to define if script is executable */
    private $compiledCondition = null;

    public function __construct(string $condition = null)
    {
        $this->condition = $condition;
    }

    /**
     * @return bool|mixed|null
     * @throws Exception
     */
    public function test()
    {
        if ($this->executable === null) {
            if ($this->compiledCondition === null) {
                throw new \LogicException(("Accreditor is not compiled."));
            }

            $this->checkSyntax();

            $this->executable = eval("return (bool) ({$this->compiledCondition});");
        }

        return $this->executable;
    }

    public function compile(ScriptFormatterInterface $formatter)
    {
        if (empty($this->condition)) {
            $this->executable = true;
        } else {
            if ($this->compiledCondition !== null) {
                throw new \LogicException(("Accreditor already compiled."));
            }

            $this->compiledCondition = $formatter->format($this->condition);
        }
    }

    protected function checkSyntax()
    {
        $code = escapeshellarg($this->compiledCondition);

        exec("echo \"<?php $code\" | php -l 2>/dev/null", $output, $return);

        if (!empty($output)) {
            $message = "Error when execute condition : `{$this->condition}` --> `{$this->compiledCondition}`.";
            throw new RuntimeException($message);
        }
    }
}