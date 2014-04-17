<?php

namespace Task\Plugin\Process;

use Symfony\Component\Process\Process as BaseProcess;
use Task\Plugin\Stream\WritableInterface;
use Task\Plugin\Stream\ReadableInterface;

class Process extends BaseProcess implements WritableInterface, ReadableInterface
{
    protected $stream;

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
        if ($this->stream) {
            $stream = $this->stream;
            $callback = function ($type, $data) use ($stream) {
                $stream->write($data);
            };
        }

        $exitcode = parent::run($callback);

        if ($this->isSuccessful()) {
            return $exitcode;
        } else {
            throw new \RuntimeException(
                sprintf(
                    "%s returned %d\n%s",
                    $this->getCommandLine(),
                    $this->getExitCode(),
                    $this->getErrorOutput()
                )
            );
        }

        return $this;
    }

    public function read()
    {
        $this->run();
        return $this->getOutput();
    }

    public function write($data)
    {
        return $this->setStdin($data);
    }

    public function pipe(WritableInterface $to)
    {
        $this->stream = $to;
        return $to->write($this->read());
    }
}
