<?php

namespace WorldFactory\QQ\Services\Runners;

class PhpRunner extends AbstractRunner
{
    /**
     * @param string $script
     */
    public function run(string $script) : void
    {
        eval($script);

        $this->getOutput()->writeln('');
    }
}