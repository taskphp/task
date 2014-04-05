<?php

namespace Task\Plugin\Filesystem;

use Task\Plugin\Stream;

class File extends \SplFileObject implements Stream\ReadableInterface, Stream\WritableInterface
{
    public function __construct($filename)
    {
        try {
            parent::__construct($filename, 'r+');
        } catch (\RuntimeException $ex) {
        }
    }

    public function read()
    {
        $this->rewind();

        $content = '';
        while (!$this->eof()) {
            $content .= $this->fgets();
        }

        return $content;
    }

    public function write($content)
    {
        $this->ftruncate(0);
        $this->fwrite($content);
        return $this;
    }

    public function append($content)
    {
        while (!$this->eof()) {
            # why doesn't next() work here?
            $this->current();
        }

        $this->fwrite($content);
        return $this;
    }

    public function pipe(Stream\WritableInterface $to)
    {
        return $to->write($this->read());
    }
}
