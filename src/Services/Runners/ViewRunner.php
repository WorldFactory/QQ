<?php

namespace WorldFactory\QQ\Services\Runners;

use Symfony\Component\Console\Helper\FormatterHelper;

class ViewRunner extends AbstractRunner
{
    const DEFAULT_STYLE = 'error';

    /**
     * @param string $script
     */
    public function run(string $script) : void
    {
        $config = $this->getCommand()->getConfig();

        $style = $config['style'] ?? self::DEFAULT_STYLE;

        $formatter = new FormatterHelper();
        $this->getOutput()->writeln($formatter->formatBlock($script, $style, TRUE));
    }

    public function isHeaderDisplayed() : bool
    {
        return false;
    }
}