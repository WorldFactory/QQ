<?php

namespace WorldFactory\QQ\Misc;

use Exception;
use Symfony\Component\Console\Output\OutputInterface;

class TemporizedExecution
{
    /** @var Buffer  */
    private $buffer;

    /** @var OutputInterface  */
    private $output;

    /** @var callable */
    private $executionHook;

    /** @var callable */
    private $failureHook;

    /** @var callable */
    private $finallyHook;

    /** @var int Buffer chunk size, in octets. */
    private $chunkSize = 2;

    /**
     * TemporizedExecution constructor.
     * @param Buffer $buffer
     * @param OutputInterface $output
     * @param callable $executionHook
     */
    public function __construct(Buffer $buffer, OutputInterface $output, callable $executionHook)
    {
        $this->buffer = $buffer;
        $this->output = $output;

        $this->executionHook = $executionHook;
        $this->failureHook = function() {};
        $this->finallyHook = function() {};
    }

    /**
     * @param callable $failureHook
     * @return self
     */
    public function setFailureHook(callable $failureHook) : self
    {
        $this->failureHook = $failureHook;

        return $this;
    }

    /**
     * @param callable $finallyHook
     * @return self
     */
    public function setFinallyHook(callable $finallyHook) : self
    {
        $this->finallyHook = $finallyHook;

        return $this;
    }

    /**
     * @param int $chunkSize
     */
    public function setChunkSize(int $chunkSize) : void
    {
        $this->chunkSize = $chunkSize;
    }

    /**
     * @param null $data
     * @throws Exception
     */
    public function execute()
    {
        ob_start([$this, 'displayCallback'], $this->chunkSize);

        try {
            call_user_func($this->executionHook);
        } catch (Exception $exception) {
            call_user_func($this->failureHook, $exception);
            throw $exception;
        } finally {
            ob_end_flush();
            call_user_func($this->finallyHook);
        }
    }

    /**
     * @param string $buffer
     * @param int|null $phase
     * @return bool
     */
    public function displayCallback(string $buffer , int $phase = null) : bool
    {
        $this->buffer->addResult($buffer);

        $this->output->write($buffer);

        return true;
    }
}