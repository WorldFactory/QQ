<?php

namespace WorldFactory\QQ\Services\Runners;

use Symfony\Component\Console\Helper\FormatterHelper;
use WorldFactory\QQ\Entities\Script;

class ViewRunner extends AbstractRunner
{
    const OPTION_DEFINITIONS = [
        'style' => [
            'type' => 'string',
            'default' => 'error',
            'description' => "The style to apply to the message."
        ]
    ];

    protected const SHORT_DESCRIPTION = "Show script in a frame. Useful to display a message.";

    protected const LONG_DESCRIPTION = <<<EOT
This Runner is used to display messages during the execution of your scripts.
You have an option to style the frame as you wish.
EOT;

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