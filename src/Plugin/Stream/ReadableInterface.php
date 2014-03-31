<?php

namespace Task\Plugin\Stream;

interface ReadableInterface
{
    public function read();
    public function pipe(WritableInterface $to);
}
