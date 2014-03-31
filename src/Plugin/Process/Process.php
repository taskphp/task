<?php

namespace Task\Plugin\Process;

use Symfony\Component\Process\Process as BaseProcess;
use Task\Plugin\Stream\WritableInterface;
use Task\Plugin\Stream\ReadableInterface;

/**
 * $project['ps']->build('whoami')
 *     ->pipe($project['ps']->build('cat'));
 *
 * $project['ps']->build('whoami')
 *     ->pipe($project['fs']->open('/path/to'));
 */
class Process extends BaseProcess implements WritableInterface, ReadableInterface
{
    public static function extend(BaseProcess $proc)
    {
        return new static(
            $proc->getCommandLine(),
            $proc->getWorkingDirectory(),
            $proc->getEnv(),
            $proc->getStdin(),
            $proc->getTimeout(),
            $proc->getOptions()
        );
    }

    public function run($callback = null)
    {
        $exitcode = parent::run($callback);

        if ($this->isSuccessful()) {
            return $exitcode;
        } else {
            throw new Exception(
                sprintf(
                    "%s returned %d: %s\n%s",
                    $this->getCommandLine(),
                    $this->getExitCode(),
                    $this->getErrorOutput(),
                    $this->getOutput()
                )
            );
        }
    }

    public function read()
    {
        $this->run();
        return $this->getOutput();
    }

    public function write($data)
    {
        return $this->setStdin($data)->run();
    }

    public function pipe(WritableInterface $to)
    {
        return $to->write($this->read());
    }
}
