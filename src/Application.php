<?php

namespace WorldFactory\QQ;

use Exception;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use WorldFactory\QQ\Misc\ConfigLoader;
use WorldFactory\QQ\Misc\BasicCommand;
use WorldFactory\QQ\Services\Commands\AboutCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application as SymfonyConsoleApplication;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WorldFactory\QQ\Services\DeprecationHandler;

class Application extends SymfonyConsoleApplication
{
    const VERSION = 'v2.1.4';
    const MAINTAINER_NAME = 'Raphaël Aurières';
    const MAINTAINER_MAIL = 'raphael.aurieres@gmail.com';

    const FILE_SRC = 'config/qq.yml';
    const FILE_OLD = 'config/commands.yml';
    const IMPORTS_FILE = 'config/imports.json';
    const CONFIG_PATH = './config/commands';

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
        "Quick Qommand, with a Q, because it's like that !",
        "And if we all had QQ tattoo on the right buttock ?"
    ];

    public function __construct(Kernel $kernel)
    {
        parent::__construct($kernel);

        $kernel->setApplication($this);
    }

    /**
     * @return ConfigLoader
     * @throws Exception
     */
    private function buildConfigLoader()
    {
        /** @var DeprecationHandler $deprecationHandler */
        $deprecationHandler = $this->getKernel()->getContainer()->get('qq.handler.deprecation');

        $configLoader = new ConfigLoader($deprecationHandler);

        if (file_exists(self::IMPORTS_FILE)) {
            $configLoader->loadImportFile(self::IMPORTS_FILE);
        }

        $src = self::FILE_SRC;
        $old = self::FILE_OLD;

        if (file_exists($src)) {
            $configLoader->loadConfigFile($src);
        } elseif (file_exists($old)) {
            $configLoader->loadConfigFile($old);

            $deprecationHandler = $this->getKernel()->getContainer()->get('qq.handler.deprecation');

            $deprecationHandler->insert("Config file '$old' is deprecated. Consider using location : '$src'.");
        } else {
            throw new Exception("Configuration file not found. Location : '$src'.");
        }

        $finder = new Finder();

        if (is_dir(self::CONFIG_PATH)) {
            $finder->files()->in(self::CONFIG_PATH)->name(['*.yml', '*.yaml'])->sortByName();

            /** @var SplFileInfo $file */
            foreach ($finder as $file) {
                $configLoader->loadConfigFile($file->getRealPath());
            }
        }

        return $configLoader;
    }

    /**
     * @return ConfigLoader
     * @throws Exception
     */
    public function getConfigLoader() : ConfigLoader
    {
        if ($this->configLoader === null) {
            $this->configLoader = $this->buildConfigLoader();
        }

        return $this->configLoader;
    }

    /**
     * Initializes all the composer commands.
     * @throws Exception
     */
    protected function getDefaultCommands()
    {
        $dynamiqueCommands = [];

        /** @var ConfigLoader $configLoader */
        $configLoader = $this->getKernel()->getContainer()->get('qq.loader.config');

        foreach ($configLoader->getCommands() as $commandDefinition) {
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

        $output->writeln($formatter->formatBlock($message, 'fg=white;bg=magenta', TRUE));

        return parent::doRun($input, $output);
    }

    private function getRandomSentence()
    {
        return $this->sentences[(int) round(mt_rand(0, count($this->sentences) - 1))];
    }
}
