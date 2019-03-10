<?php

namespace WorldFactory\QQ\Services;

use Exception;
use WorldFactory\QQ\Interfaces\ScriptFormatterInterface;
use WorldFactory\QQ\Misc\ConfigLoader;

class ScriptFormatter implements ScriptFormatterInterface
{
    const REGEX_ENV_VAR_LEGACY = '/(?<match>%ENV:(?<key>[a-zA-Z0-9_]+)%)/';
    const REGEX_ENV_VAR_MATCH = '/(^|[^\\\\])(?<match>\$\{(?<key>[a-zA-Z0-9_]+)\})/';
    const REGEX_ENV_VAR_REPLACE = '/(^|[^\\\\])(\$\{%s\})/';
    const REGEX_ENV_VAR_CLEANING = '/(\\\\)(\$\{[a-zA-Z0-9_]+\})/';

    /** @var ConfigLoader */
    private $configLoader;

    /** @var array */
    private $args = [];

    /** @var array */
    private $usedArgs = [];

    /**
     * VarFormatter constructor.
     * @param ConfigLoader $configLoader
     */
    public function __construct(ConfigLoader $configLoader)
    {
        $this->configLoader = $configLoader;
    }

    public function setTokens(array $tokens) : void
    {
        $this->args = $tokens;
        $this->usedArgs = [];
    }

    public function sanitize(string $var) : string
    {
        if (preg_match(RunnerFactory::PROTOCOL_REGEX, $var, $result)) {
            $header = $result['header'];

            $var = substr($var, strlen($header));
        }

        return $var;
    }

    public function format(string $var) : string
    {
        $var = $this->injectEnvVars($var);
        $var = $this->injectParameters($var);
        $var = $this->injectArguments($var);

        return $var;
    }

    public function finalize(string $var) : string
    {
        $var = $this->injectAllArguments($var);
        $var = $this->injectLeftArguments($var);

        return $var;
    }

    protected function injectArguments($var) : string
    {
        foreach ($this->args as $i => $arg) {
            $index = $i + 1;
            if (preg_match("/%$index%/", $var)) {
                $var = str_replace('%' . $index . '%', $arg, $var);
                $this->usedArgs[] = $index;
            }
        }

        return $var;
    }

    protected function injectParameters($var) : string
    {
        $parameters = $this->configLoader->getParameters();

        foreach ($parameters as $key => $val) {
            $var = str_replace('%' . $key . '%', $val, $var);
        }

        return $var;
    }

    protected function injectEnvVars($var) : string
    {
        if (preg_match_all(self::REGEX_ENV_VAR_LEGACY, $var, $matches)) {
            trigger_error("%ENV:VARNAME% format is deprecated for variable environment injection. Use \${VARNAME} instead.", E_USER_DEPRECATED);

            $combined = array_combine($matches['key'], $matches['match']);

            foreach ($combined as $key => $match) {
                if (!array_key_exists($key, $_ENV)) {
                    throw new Exception("Target env var '$key' is not defined.");
                }

                $var = str_replace($match, $_ENV[$key], $var);
            }
        }

        if (preg_match_all(self::REGEX_ENV_VAR_MATCH, $var, $matches)) {
            $combined = array_combine($matches['key'], $matches['match']);

            foreach ($combined as $key => $match) {
                if (!array_key_exists($key, $_ENV)) {
                    throw new Exception("Target env var '$key' is not defined.");
                }

                $pattern = sprintf(self::REGEX_ENV_VAR_REPLACE, $key);
                $var = preg_replace($pattern, '${1}' . $_ENV[$key], $var);
            }
        }

        $var = preg_replace(self::REGEX_ENV_VAR_CLEANING, '$2', $var);

        return $var;

        foreach ($_ENV as $key => $val) {
            $var = str_replace('%ENV:' . $key . '%', $val, $var);
        }

        return $var;
    }

    protected function injectAllArguments($var) : string
    {
        if (preg_match("/%_all%/", $var)) {
            $var = str_replace('%_all%', implode(' ', $this->args), $var);
        }

        return $var;
    }

    protected function injectLeftArguments($var) : string
    {
        if (preg_match("/%_left%/", $var)) {
            $leftArgs = [];

            foreach ($this->args as $key => $arg) {
                if (!in_array($key + 1, $this->usedArgs)) {
                    $leftArgs[] = $arg;
                }
            }

            $var = str_replace('%_left%', implode(' ', $leftArgs), $var);
        }

        return $var;
    }
}