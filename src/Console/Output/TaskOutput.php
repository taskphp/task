<?php

namespace Task\Console\Output;

use Symfony\Component\Console\Output\ConsoleOutput;
use Task\Plugin\Stream\WritableInterface;

class TaskOutput extends ConsoleOutput implements WritableInterface
{
}
