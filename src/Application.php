<?php

namespace WorldFactory\QQ;

use WorldFactory\QQ\Misc\ConfigLoader;
use WorldFactory\QQ\Misc\BasicCommand;
use WorldFactory\QQ\Services\Commands\AboutCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application as SymfonyConsoleApplication;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class Application extends SymfonyConsoleApplication
{
    const VERSION = 'v1.4.3';
    const MAINTAINER_NAME = 'Raphaël Aurières';
    const MAINTAINER_MAIL = 'raphael.aurieres@gmail.com';

    /** @var ConfigLoader Chargeur de configuration QQ. */
    private $configLoader;

    private $sentences = [
        "Small software super good even if you still find some bugs.",
        "Super death launcher that even your grandmother dreams of using it.",
        "Not yet a launcher, THE launcher !!",
        "Why do you spend your time reading this hook when you know it is useless ?",
        "A laughing developer is a developer ... who laughs !!",
        "Why two Q ? If you are asked, you will say that you do not know it ...",
        "Lorem ipsum dolor sit amet adipicim consectuetur... Or something like that.",
        "Remember to update QQ to get new duff messages. ;)",
        "May the force be with QQ...",
        "The origin of most bugs lies in the interface between the chair and the keyboard.",
        "Why two Q ? If i tell you, i'll have to kill you ...",
        "Quick Qommand, with a Q, because it's like that !"
    ];

    public function __construct(Kernel $kernel)
    {
        parent::__construct($kernel);

        $kernel->setApplication($this);
    }

    public function setConfigLoader(ConfigLoader $configLoader)
    {
        $this->configLoader = $configLoader;

        return $this;
    }

    public function getConfigLoader() : ConfigLoader
    {
        return $this->configLoader;
    }

    /**
     * Initializes all the composer commands.
     */
    protected function getDefaultCommands()
    {
        $dynamiqueCommands = [];

        foreach ($this->configLoader->getCommands() as $commandDefinition) {
            /** @var BasicCommand $command */
            $command = new BasicCommand($commandDefinition);

            $dynamiqueCommands[] = $command;

            $command->setContainer($this->getKernel()->getContainer());
        }

        $commands = array_merge(parent::getDefaultCommands(), [new AboutCommand()], $dynamiqueCommands);

        return $commands;
    }

    /**
     * Runs the current application.
     *
     * @return int 0 if everything went fine, or an error code
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $formatter = new FormatterHelper();

        $message = [
            "QQ - Quick Qommand - " . self::VERSION,
            $this->getRandomSentence()
        ];

        $output->writeln($formatter->formatBlock($message, 'question', TRUE));

        return parent::doRun($input, $output);
    }

    private function getRandomSentence()
    {
        return $this->sentences[(int) round(mt_rand(0, count($this->sentences) - 1))];
    }
}
