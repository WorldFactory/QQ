<?php

namespace WorldFactory\QQ\Services\Runners;

use Symfony\Component\Console\Helper\FormatterHelper;
use WorldFactory\QQ\Foundations\AbstractRunner;

class ViewRunner extends AbstractRunner
{
    const OPTION_DEFINITIONS = [
        'style' => [
            'type' => 'string',
            'default' => 'fg=white;bg=cyan',
            'description' => "The style to apply to the message."
        ]
    ];

    protected const SHORT_DESCRIPTION = "Show script in a frame. Useful to display a message.";

    protected const LONG_DESCRIPTION = <<<EOT
This Runner is used to display messages during the execution of your scripts.
You have an option to style the frame as you wish.
EOT;

    /**
     * @inheritdoc
     */
    public function execute(string $script) : void
    {
        $formatter = new FormatterHelper();

        $message = $formatter->formatBlock(
            $script,
            $this->getOption('style'),
            TRUE
        );

        $this->getOutput()->writeln($message);

        $this->getBuffer()->setResult($script);
    }

    public function isHeaderDisplayed() : bool
    {
        return false;
    }
}