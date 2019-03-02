<?php

namespace WorldFactory\QQ\Entities;

use WorldFactory\QQ\Interfaces\RunnerInterface;
use WorldFactory\QQ\Interfaces\ScriptFormatterInterface;

class Script
{
    /** @var string Configured script */
    private $script;

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

    /** @var Accreditor Object to define if script is executable */
    private $accreditor;

    public function __construct($script, string $type, array $tokens, ScriptConfig $options, Accreditor $accreditor)
    {
        $this->type = $type;
        $this->tokens = $tokens;
        $this->options = $options;
        $this->accreditor = $accreditor;

        $this->parseScript($script);
    }

    private function parseScript($script)
    {
        if (is_string($script)) {
            $this->script = $script;
        } elseif (is_array($script) && array_key_exists('script', $script)) {
            $this->type = $script['type'] ?? $this->type;
            $this->options = $this->options->merge($script['options'] ?? []);
            $this->accreditor = new Accreditor($script['if'] ?? null);
            $this->parseScript($script['script']);
        } elseif (is_array($script)) {
            foreach ($script as $item) {
                $this->children[] = new Script($item, $this->type, $this->tokens, $this->options->clone(), new Accreditor());
            }
        } else {
            throw new \InvalidArgumentException("Unknown script type.");
        }
    }

    public function isExecutable() : bool
    {
        return $this->accreditor->test();
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
        return !empty($this->children);
    }

    /**
     * @return array
     */
    public function getChildren() : array
    {
        return $this->children;
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
        $this->accreditor->compile($this->formatter);

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