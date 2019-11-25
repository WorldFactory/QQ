<?php

namespace WorldFactory\QQ\Services;


use Monolog\Handler\AbstractProcessingHandler;

class DeprecationHandler extends AbstractProcessingHandler
{
    private $deprecations = [];

    protected function write(array $record) : void
    {
        if (preg_match("/^User Deprecated: (?<text>.*)$/", $record['message'], $matches)) {
            $this->deprecations[] = $matches['text'];
        }
    }

    /**
     * @return array
     */
    public function getDeprecations() : array
    {
        sort($this->deprecations);

        return array_unique($this->deprecations);
    }
}