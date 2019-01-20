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

    /** @var array Runner options */
    private $options = [];

    private $children = [];

    /** @var ScriptFormatterInterface */
    private $formatter;

    /** @var RunnerInterface */
    private $runner;

    public function __construct($script, array $tokens, array $options)
    {
        $this->tokens = $tokens;
        $this->options = $options;

        $this->parseScript($script);
    }

    private function parseScript($script)
    {
        if (is_string($script)) {
            $this->script = $script;
        } elseif (is_array($script) && array_key_exists('script', $script)) {
            $this->options = array_merge($this->options, $script['options'] ?? []);
            $this->parseScript($script['script']);
        } elseif (is_array($script)) {
            foreach ($script as $item) {
                $this->children[] = new Script($item, $this->tokens, $this->options);
            }
        } else {
            throw new \InvalidArgumentException("Unknown script type.");
        }
    }

    public function getIterator()
    {
        if ($this->hasChildren()) {
            $array = [];

            /** @var Script $child */
            foreach ($this->children as $child) {
                $array[] = $child->getIterator();
            }
        } else {
            $array = $this;
        }

        return $array;
    }

    /**
     * @return string
     */
    public function getScript() : string
    {
        if ($this->hasChildren()) {
            throw new \LogicException("Aggregated script has no text script.");
        }

        return $this->script;
    }

    /**
     * @return string
     */
    public function getCompiledScript(): string
    {
        if ($this->hasChildren()) {
            throw new \LogicException("Aggregated script has no compiled script.");
        }

        if ($this->compiledScript === null) {
            $this->compile();
        }

        return $this->compiledScript;
    }

    /**
     * @param ScriptFormatterInterface $formatter
     */
    public function setFormatter(ScriptFormatterInterface $formatter): void
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

    public function hasChildren() : bool
    {
        return !empty($this->children);
    }

    /**
     * @return array
     */
    public function getChildren(): array
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
     * @return array
     */
    public function getOptions() : array
    {
        return $this->options;
    }

    public function getOption(string $name)
    {
        return $this->options[$name] ?? null;
    }

    public function hasOption(string $name)
    {
        return array_key_exists($name, $this->options);
    }

    public function compile()
    {
        if ($this->hasChildren()) {
            throw new \LogicException("Unable to compile an aggregated script.");
        }

        if ($this->compiledScript !== null) {
            throw new \LogicException(("Script already compiled."));
        }

        $compiledScript = $this->getScript();

        $compiledScript = $this->formatter->sanitize($compiledScript);
        $compiledScript = $this->formatter->format($compiledScript);

        $compiledScript = $this->runner->format($compiledScript);

        $compiledScript = $this->formatter->finalize($compiledScript);

        $this->compiledScript = $compiledScript;
    }

    public function execute()
    {
        $this->runner->run($this->getCompiledScript());
    }
}