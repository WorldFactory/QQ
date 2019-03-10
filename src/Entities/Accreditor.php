<?php

namespace WorldFactory\QQ\Entities;

use Exception;
use DateTime;
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
     * @return string|null
     */
    public function getCompiledCondition() :? string
    {
        return $this->compiledCondition;
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

            $this->run();
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

    protected function run()
    {
        $filename = $this->buildScript();

        try {
            $this->check($filename);

            $this->executable = $this->execute($filename);
        } finally {
            unlink($filename);
        }
    }

    protected function check(string $filename)
    {
        $code = "php -l $filename 2>/dev/null";

        exec($code, $output, $return);

        if ($return !== 0) {
            $message = "Error when parsing condition : `{$this->condition}` --> `{$this->compiledCondition}`.";
            throw new RuntimeException($message, $return);
        }
    }

    protected function execute(string $filename)
    {
        $code = "php -f $filename 2>&1";

        exec($code, $output, $return);

        if ($return !== 0) {
            $message = array_shift($output);
            $message = str_replace(" in $filename:1", '', $message);
            $message = "Error when running condition `{$this->compiledCondition}` : $message";

            throw new RuntimeException($message, $return);
        }

        return (bool) array_shift($output);
    }

    protected function buildScript() : string
    {
        $varDir = getcwd() . '/var/tmp';

        if (!is_dir($varDir)) {
            mkdir($varDir);
        }

        $hash = sha1((DateTime::createFromFormat('U.u', microtime(TRUE)))->format('Y-m-d H:i:s:u'));

        $tmpScriptName = $varDir . '/' . $hash . '.php';

        $conditionScript = '<?php echo ((bool) (' . $this->compiledCondition . ')) ? 1 : 0;';

        file_put_contents($tmpScriptName, $conditionScript);

        return $tmpScriptName;
    }
}