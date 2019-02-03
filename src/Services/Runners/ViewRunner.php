<?php

namespace WorldFactory\QQ\Services\Runners;

use Symfony\Component\Console\Helper\FormatterHelper;
use WorldFactory\QQ\Entities\Script;

class ViewRunner extends AbstractRunner
{
    const OPTION_DEFINITIONS = [
        'style' => ['type' => 'string', 'default' => 'error']
    ];

    protected const SHORT_DESCRIPTION = "Show script in a frame. Useful to display a message.";

    /**
     * @param Script $script
     */
    public function run(Script $script) : void
    {
        $formatter = new FormatterHelper();

        $message = $formatter->formatBlock(
            $script->getCompiledScript(),
            $script->getOption('style'),
            TRUE
        );

        $this->getOutput()->writeln($message);
    }

    public function isHeaderDisplayed() : bool
    {
        return false;
    }
}