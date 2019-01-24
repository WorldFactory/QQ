<?php

namespace WorldFactory\QQ\Services\Runners;

use Symfony\Component\Console\Helper\FormatterHelper;
use WorldFactory\QQ\Entities\Script;

class ViewRunner extends AbstractRunner
{
    const DEFAULT_STYLE = 'error';

    /**
     * @param Script $script
     */
    public function run(Script $script) : void
    {
        $style = $script->hasOption('style') ? $script->getOption('style') : self::DEFAULT_STYLE;

        $formatter = new FormatterHelper();
        $this->getOutput()->writeln($formatter->formatBlock($script->getCompiledScript(), $style, TRUE));
    }

    public function isHeaderDisplayed() : bool
    {
        return false;
    }
}