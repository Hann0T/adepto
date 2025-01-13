<?php

namespace Adepto\Bus;

use Adepto\Bus\Contracts\Queue\Queue as QueueContract;

class QueueNode
{
    public ?QueueNode $next;
    public mixed $value;

    public function __construct(mixed $value)
    {
        $this->next = null;
        $this->value = $value;
    }
}

class Queue implements QueueContract
{
    public ?QueueNode $head;
    public ?QueueNode $tail;
    public int $len;

    public function __construct()
    {
        $this->head = null;
        $this->tail = null;
        $this->len = 0;
    }

    public function enqueue(mixed $value): void
    {
        $node = new QueueNode($value);
        $this->len++;

        if (!$this->head and !$this->tail) {
            $this->head = $node;
            $this->tail = $node;
            return;
        }

        $this->tail->next = $node;
        $this->tail = $node;
    }

    public function dequeue(): mixed
    {
        if ($this->len <= 0) return null;
        if (!$this->head) {
            $this->tail = null;
            return null;
        }

        $this->len--;

        $value = $this->head?->value;
        $this->head = $this->head?->next;

        if ($this->len == 0) {
            $this->tail = null;
            $this->head = null;
        }

        return $value;
    }
}
