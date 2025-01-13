<?php

namespace Adepto\Bus\Contracts\Queue;

interface Queue {
    public function enqueue(mixed $value): void;
    public function dequeue(): mixed;
}
