<?php

namespace WorldFactory\QQ\Entities;

use WorldFactory\QQ\Interfaces\RunnerInterface;
use WorldFactory\QQ\Interfaces\ScriptFormatterInterface;

class Script
{
    /** @var string Configured script */
    private $script;

    /** @var string Configured script */
    private $thenChildren;

    /** @var string Configured script */
    private $elseChildren;

    /** @var string Compiled script */
    private $compiledScript = null;

    /** @var array CLI arguments */
    private $tokens = [];

    /** @var ScriptConfig Runner options */
    private $options = [];

    private $children = [];

    /** @var ScriptFormatterInterface */
    private $formatter;

    /** @var RunnerInterface */
    private $runner;

    /** @var string Type of runner to be used */
    private $type;

    /** @var Accreditor|null Object to define if script is executable */
    private $accreditor = null;

    public function __construct(array $definition, string $type, array $tokens, ScriptConfig $options)
    {
        $this->type = $definition['type'] ?? $type;
        $this->tokens = $tokens;
        $this->options = $options->merge($definition['options'] ?? []);
        $this->accreditor = isset($definition['if']) ? new Accreditor($definition['if']) : null;

        if ($this->isConditionnal()) {
            if (isset($definition['then'])) {
                $script = $definition['then'];
            } elseif (isset($definition['script'])) {
                $script = $definition['script'];
                trigger_error("For conditional scripts, you must provide the 'then' option.", E_USER_DEPRECATED);
            } else {
                throw new \Exception("'then' option is not provided.");
            }

            $this->thenChildren = $this->parseScript($script, true);

            if (isset($definition['else'])) {
                $this->elseChildren = $this->parseScript($definition['else'], true);
            }
        } else {
            if (!isset($definition['script'])) {
                throw new \Exception("'script' option is not provided.");
            }

            $script = $this->parseScript($definition['script']);

            if (is_array($script)) {
                $this->children = $script;
            } else {
                $this->script = $script;
            }
        }
    }

    private function parseScript($script, $forceScript = false)
    {
        $result = null;

        if (is_string($script)) {
            $result = $forceScript ? [new Script(['script' => $script], $this->type, $this->tokens, $this->options->clone())] : $script;
        } elseif (is_array($script) && (array_key_exists('script', $script) || array_key_exists('if', $script))) {
            $result = [new Script($script, $this->type, $this->tokens, $this->options->clone())];
        } elseif (is_array($script)) {
            $result = [];

            foreach ($script as $item) {
                $result[] = new Script(['script' => $item], $this->type, $this->tokens, $this->options->clone());
            }
        } else {
            throw new \InvalidArgumentException("Unknown script type.");
        }

        return $result;
    }

    public function isConditionnal() : bool
    {
        return ($this->accreditor !== null);
    }

    public function isExecutable() : bool
    {
        $result = true;

        if ($this->isConditionnal()) {
            $result = $this->accreditor->test();
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getType() : string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getScript() :? string
    {
        return $this->script;
    }

    /**
     * @return string
     */
    public function getCompiledScript() : string
    {
        if ($this->hasChildren()) {
            throw new \LogicException("Aggregated script has no compiled script.");
        } elseif ($this->compiledScript === null) {
            throw new \LogicException("Script is not compiled.");
        }

        return $this->compiledScript;
    }

    /**
     * @param ScriptFormatterInterface $formatter
     */
    public function setFormatter(ScriptFormatterInterface $formatter) : void
    {
        $this->formatter = $formatter;

        $formatter->setTokens($this->getTokens());
    }

    /**
     * @param RunnerInterface $runner
     */
    public function setRunner(RunnerInterface $runner) : void
    {
        $this->runner = $runner;
    }

    /**
     * @return RunnerInterface
     */
    public function getRunner() : RunnerInterface
    {
        return $this->runner;
    }

    public function hasChildren() : bool
    {
        if ($this->isConditionnal()) {
            $result = $this->accreditor->test() ? !empty($this->thenChildren) : !empty($this->elseChildren);
        } else {
            $result = !empty($this->children);
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getChildren() : array
    {
        if ($this->isConditionnal()) {
            $result = $this->accreditor->test() ? $this->thenChildren : $this->elseChildren;
        } else {
            $result = $this->children;
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getTokens() : array
    {
        return $this->tokens;
    }

    /**
     * @return ScriptConfig
     */
    public function getOptions() : ScriptConfig
    {
        return $this->options;
    }

    public function getOption(string $name)
    {
        return $this->options[$name];
    }

    public function hasOption(string $name) : bool
    {
        return isset($this->options[$name]);
    }

    public function compile() : void
    {
        if ($this->compiledScript !== null) {
            throw new \LogicException(("Script already compiled."));
        }

        $this->options->setOptionDefinitions($this->runner->getOptionDefinitions());

        $this->options->compile($this->formatter);

        $this->compiledScript = $this->compileScript();
    }

    protected function compileScript() :? string
    {
        if ($this->isConditionnal()) {
            $this->accreditor->compile($this->formatter);
        }

        $compiledScript = $this->getScript();

        if (!empty($compiledScript)) {
            $compiledScript = $this->formatter->sanitize($compiledScript);
            $compiledScript = $this->formatter->format($compiledScript);

            $compiledScript = $this->runner->format($this, $compiledScript);

            $compiledScript = $this->formatter->finalize($compiledScript);
        }

        return $compiledScript;
    }

    public function execute() : void
    {
        $this->runner->run($this);
    }
}