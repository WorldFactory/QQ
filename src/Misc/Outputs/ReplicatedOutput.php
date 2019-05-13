<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WorldFactory\QQ\Misc\Outputs;

use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\ConsoleSectionOutput;
use Symfony\Component\Console\Output\OutputInterface;
use WorldFactory\QQ\Misc\Buffer;

/**
 * Decorates output to catch write messages.
 */
class ReplicatedOutput implements ConsoleOutputInterface
{
    private $output;

    private $buffer;

    public function __construct(OutputInterface $output, Buffer $buffer)
    {
        $this->output = $output;
        $this->buffer = $buffer;
    }

    public function getOriginalOutput() : OutputInterface
    {
        return $this->output;
    }

    /**
     * {@inheritdoc}
     */
    public function write($messages, $newline = false, $type = OutputInterface::OUTPUT_NORMAL)
    {
        $this->output->write($messages, $newline, $type);

        if (is_array($messages)) {
            $messages = join(PHP_EOL, $messages);
        }

        $this->buffer->addResult($messages . ($newline ? PHP_EOL : ''));
    }

    /**
     * {@inheritdoc}
     */
    public function writeln($messages, $type = OutputInterface::OUTPUT_NORMAL)
    {
        $this->output->writeln($messages, $type);

        if (is_array($messages)) {
            $messages = join(PHP_EOL, $messages);
        }

        $this->buffer->addResult($messages . PHP_EOL);
    }

    /**
     * {@inheritdoc}
     */
    public function setVerbosity($level)
    {
        $this->output->setVerbosity($level);
    }

    /**
     * {@inheritdoc}
     */
    public function getVerbosity()
    {
        return $this->output->getVerbosity();
    }

    /**
     * {@inheritdoc}
     */
    public function setDecorated($decorated)
    {
        $this->output->setDecorated($decorated);
    }

    /**
     * {@inheritdoc}
     */
    public function isDecorated()
    {
        return $this->output->isDecorated();
    }

    /**
     * {@inheritdoc}
     */
    public function setFormatter(OutputFormatterInterface $formatter)
    {
        $this->output->setFormatter($formatter);
    }

    /**
     * {@inheritdoc}
     */
    public function getFormatter()
    {
        return $this->output->getFormatter();
    }

    /**
     * {@inheritdoc}
     */
    public function isQuiet()
    {
        return $this->output->isQuiet();
    }

    /**
     * {@inheritdoc}
     */
    public function isVerbose()
    {
        return $this->output->isVerbose();
    }

    /**
     * {@inheritdoc}
     */
    public function isVeryVerbose()
    {
        return $this->output->isVeryVerbose();
    }

    /**
     * {@inheritdoc}
     */
    public function isDebug()
    {
        return $this->output->isDebug();
    }

    public function getErrorOutput()
    {
        if ($this->output instanceof ConsoleOutputInterface) {
            return $this->output->getErrorOutput();
        }

        return $this->output;
    }

    public function setErrorOutput(OutputInterface $error)
    {
        if ($this->output instanceof ConsoleOutputInterface) {
            $this->output->setErrorOutput($error);
        }
    }

    /**
     * Creates a new output section.
     */
    public function section(): ConsoleSectionOutput
    {
        if ($this->output instanceof ConsoleOutputInterface) {
            return $this->output->section();
        }
    }
}
