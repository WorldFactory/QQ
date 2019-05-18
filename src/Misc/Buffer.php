<?php

namespace WorldFactory\QQ\Misc;

class Buffer
{
    /** @var string The output of the command */
    private $result = '';

    /**
     * @return string
     */
    public function __toString() : string
    {
        return $this->result;
    }

    public function reset()
    {
        $this->result = '';
    }

    /**
     * @param string $result
     */
    public function setResult($result) : void
    {
        $this->result = $result;
    }

    public function addResult(string $result) : void
    {
        $this->result .= $result;
    }
}