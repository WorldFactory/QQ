<?php

namespace WorldFactory\QQ\Interfaces;


interface ScriptFormatterInterface
{
    public function sanitize(string $var) : string;

    public function format(string $var) : string;

    public function finalize(string $var) : string;
}