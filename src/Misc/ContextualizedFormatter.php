<?php

namespace WorldFactory\QQ\Misc;

use Exception;
use WorldFactory\QQ\Components\Context;
use WorldFactory\QQ\Interfaces\ScriptFormatterInterface;
use WorldFactory\QQ\Services\RunnerFactory;

/**
 * Class ContextualizedFormatter
 * @package WorldFactory\QQ\Misc
 */
class ContextualizedFormatter implements ScriptFormatterInterface
{
    const REGEX_ENV_VAR_MATCH = '/(^|[^\\\\])(?<match>\$\{(?<key>[a-zA-Z0-9_]+)\})/';
    const REGEX_ENV_VAR_REPLACE = '/(^|[^\\\\])(\$\{%s\})/';
    const REGEX_ENV_VAR_CLEANING = '/(\\\\)(\$\{[a-zA-Z0-9_]+\})/';

    const REGEX_PARAMETER_MATCH = '/(^|[^\\\\])(?<match>\%\{(?<key>[a-zA-Z0-9_-]+)\})/';
    const REGEX_PARAMETER_REPLACE = '/(^|[^\\\\])(%%\{%s\})/';
    const REGEX_PARAMETER_CLEANING = '/(\\\\)(\%\{[a-zA-Z0-9_-]+\})/';

    /** @var Context */
    private $context;

    /** @var array */
    private $tokens = [];

    /** @var array */
    private $usedArgs = [];

    /**
     * ContextualizedFormatter constructor.
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        $this->context = $context;
        $this->tokens = $context->getTokens();
    }

    /**
     * @param string $var
     * @return string
     */
    public function sanitize(string $var) : string
    {
        if (preg_match(RunnerFactory::PROTOCOL_REGEX, $var, $result)) {
            $header = $result['header'];

            $var = substr($var, strlen($header));
        }

        return $var;
    }

    /**
     * @param string $var
     * @return string
     * @throws Exception
     */
    public function format(string $var) : string
    {
        $var = $this->injectEnvVars($var);
        $var = $this->injectParameters($var);
        $var = $this->injectTokens($var);

        return $var;
    }

    /**
     * @param string $var
     * @return string
     */
    public function finalize(string $var) : string
    {
        $var = $this->injectAllArguments($var);
        $var = $this->injectLeftArguments($var);

        return $var;
    }

    /**
     * @param string $var
     * @return string
     */
    public function injectTokens(string $var) : string
    {
        foreach ($this->tokens as $i => $arg) {
            $index = $i + 1;
            if (preg_match("/%$index%/", $var)) {
                $var = str_replace('%' . $index . '%', $arg, $var);
                $this->usedArgs[] = $index;
            }
        }

        return $var;
    }

    /**
     * @param string $var
     * @return string
     */
    public function injectParameters(string $var) : string
    {
        $parameters = $this->context->getParameters();

        foreach ($parameters as $key => $val) {
            if (preg_match("/%$key%/", $var)) {
                $var = str_replace('%' . $key . '%', $val, $var);

                trigger_error('Parameter format \'%' . $key . '%\' is deprecated. Consider using new format : \'%{' . $key . '}\'. In the future, only \'%my_param\' will be needed for simple cases.', E_USER_DEPRECATED);
            }
        }

        if (preg_match_all(self::REGEX_PARAMETER_MATCH, $var, $matches)) {
            $combined = array_combine($matches['key'], $matches['match']);

            foreach ($combined as $key => $match) {
                if (preg_match('/^[0-9]+$/', $key)) {
                    if (!isset($this->tokens[$key - 1])) {
                        throw new Exception("Target token '$key' is not defined.");
                    }

                    $value = $this->tokens[$key - 1];
                } else {
                    if (!array_key_exists($key, $parameters)) {
                        throw new Exception("Target parameter '$key' is not defined.");
                    }

                    $value = $parameters[$key];
                }

                $pattern = sprintf(self::REGEX_PARAMETER_REPLACE, $key);
                $var = preg_replace($pattern, '${1}' . $value, $var);
            }
        }

        $var = preg_replace(self::REGEX_PARAMETER_CLEANING, '$2', $var);

        return $var;
    }

    /**
     * @param string $var
     * @return string
     * @throws Exception
     */
    public function injectEnvVars(string $var) : string
    {
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
    }

    public function injectEnvVarsRecursively(array $parameters)
    {
        $formattedParameters = [];

        foreach($parameters as $key => $val) {
            if (is_array($val)) {
                $formattedParameters[$key] = $this->injectEnvVarsRecursively($val);
            } elseif (is_string($val)) {
                $formattedParameters[$key] = $this->injectEnvVars((string) $val);
            } else {
                $formattedParameters[$key] = $val;
            }
        }

        return $formattedParameters;
    }

    /**
     * @param string $var
     * @return string
     */
    protected function injectAllArguments(string $var) : string
    {
        if (preg_match("/%_all%/", $var)) {
            $var = str_replace('%_all%', implode(' ', $this->tokens), $var);
        }

        return $var;
    }

    /**
     * @param string $var
     * @return string
     */
    protected function injectLeftArguments(string $var) : string
    {
        if (preg_match("/%_left%/", $var)) {
            $leftArgs = [];

            foreach ($this->tokens as $key => $arg) {
                if (!in_array($key + 1, $this->usedArgs)) {
                    $leftArgs[] = $arg;
                }
            }

            $var = str_replace('%_left%', implode(' ', $leftArgs), $var);
        }

        return $var;
    }
}