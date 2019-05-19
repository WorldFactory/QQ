<?php

namespace WorldFactory\QQ\Services\Runners;

use Exception;
use WorldFactory\QQ\Foundations\AbstractRunner;
use WorldFactory\QQ\Misc\TemporizedExecution;

class BlobRunner extends AbstractRunner
{
    protected const SHORT_DESCRIPTION = "Run script in CLI with 'passthru' PHP function.";

    protected const LONG_DESCRIPTION = <<<EOT
Run script using 'passthru' PHP function.
This Runner is configured to bufferize the entire output of the executed command. This is to preserve its content. This behavior is more suitable in the case of a binary output.
Only one point will be displayed every 1024 bytes of data transferred.
Finally, the total size of the data will be displayed.
EOT;

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function execute(string $script)
    {
        $execution = new TemporizedExecution($this->getOutput(), function() use ($script) {
            passthru($script, $returnCode);

            if ($returnCode) {
                throw new Exception("Unknown system error : '$returnCode' for command : $script");
            }
        });

        $buffer = $execution->getBuffer();
        $output = $this->getOutput();

        $execution->setDisplayCallback(function($data) use ($buffer, $output) {
            $buffer->add($data);
            $output->write('.');
        });

        $execution->setChunkSize(1024);

        $output->write('Processing : ');

        $execution->execute();

        $output->writeln('<fg=white;bg=green> OK </>');
        $output->writeln('Data length : ' . $this->formatBytes(strlen($buffer->get())));

        return $buffer->get();
    }

    protected function formatBytes(int $bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}