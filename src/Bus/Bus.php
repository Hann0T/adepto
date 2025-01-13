<?php

namespace Adepto\Bus;

use Adepto\Bus\Contracts\Queue\ShouldQueue;
use Adepto\Bus\Contracts\Queue\Queue;

class Bus
{
    public Queue $queue;

    public function __construct(Queue $queue)
    {
        $this->queue = $queue;
    }

    public function dispatch(ShouldQueue $job): void
    {
        $this->queue->enqueue($job);
    }
}
