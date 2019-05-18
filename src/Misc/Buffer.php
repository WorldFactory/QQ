<?php

namespace WorldFactory\QQ\Misc;

class Buffer
{
    /** @var string The output of the command */
    private $content = '';

    public function reset()
    {
        $this->content = '';
    }

    /**
     * @return string
     */
    public function get() : string
    {
        return $this->content;
    }

    /**
     * @param string $result
     */
    public function set(string $result) : void
    {
        $this->content = $result;
    }

    public function add(string $result) : void
    {
        $this->content .= $result;
    }
}