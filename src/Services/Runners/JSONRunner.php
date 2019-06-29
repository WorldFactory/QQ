<?php

namespace WorldFactory\QQ\Services\Runners;

use Exception;
use WorldFactory\QQ\Foundations\AbstractRunner;

class JSONRunner extends AbstractRunner
{
    protected const OPTION_DEFINITIONS = [
        'trim' => [
            'type' => 'bool',
            'description' => "Trim result if it's a string.",
            'default' => true
        ]
    ];

    protected const SHORT_DESCRIPTION = "Parse JSON string.";

    protected const LONG_DESCRIPTION = <<<EOT
Parse a JSON string and return it in result.
Usefull with SET/FROM statement.
EOT;

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function execute(string $script)
    {
        $options = $this->getOptions();

        $result = json_decode($script);

        $status = json_last_error();

        switch($status) {
            case JSON_ERROR_DEPTH:
                throw new Exception("The maximum stack depth has been exceeded.");
                break;
            case JSON_ERROR_STATE_MISMATCH:
                throw new Exception("Invalid or malformed JSON.");
                break;
            case JSON_ERROR_CTRL_CHAR:
                throw new Exception("Control character error, possibly incorrectly encoded.");
                break;
            case JSON_ERROR_SYNTAX:
                throw new Exception("JSON syntax error.");
                break;
            case JSON_ERROR_UTF8:
                throw new Exception("Malformed UTF-8 characters, possibly incorrectly encoded.");
                break;
            case JSON_ERROR_RECURSION:
                throw new Exception("One or more recursive references in the value to be encoded.");
                break;
            case JSON_ERROR_INF_OR_NAN:
                throw new Exception("One or more NAN or INF values in the value to be encoded.");
                break;
            case JSON_ERROR_UNSUPPORTED_TYPE:
                throw new Exception("A value of a type that cannot be encoded was given.");
                break;
            case JSON_ERROR_INVALID_PROPERTY_NAME:
                throw new Exception("A property name that cannot be encoded was given.");
                break;
            case JSON_ERROR_UTF16:
                throw new Exception("Malformed UTF-16 characters, possibly incorrectly encoded.");
                break;
            case JSON_ERROR_NONE:
                break;
            default:
                throw new Exception("Unknown JSON error : '$status.");
                break;
        }

        return (is_string($result) && $options['trim']) ? trim($result) : $result;
    }
}