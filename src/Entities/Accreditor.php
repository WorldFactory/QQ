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

            try {
                $this->executable = eval("return (bool) ({$this->compiledCondition});");
            } catch (ParseError $exception) {
                $message = "Error when execute condition : '{$this->condition}' : " . $exception->getMessage();
                throw new RuntimeException($message, 0, $exception);
            }
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
}