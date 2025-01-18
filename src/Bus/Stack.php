<?php

namespace Adepto\Bus;

class StackNode
{
    public ?StackNode $prev;
    public mixed $value;

    public function __construct(mixed $value)
    {
        $this->prev = null;
        $this->value = $value;
    }
}

class Stack
{
    public ?StackNode $tail = null;
    public int $len = 0;

    public function push(mixed $value): void
    {
        $node = new StackNode($value);
        $this->len++;
        if (!$this->tail) {
            $this->tail = $node;
            return;
        }

        $prev = $this->tail;
        $node->prev = $prev;
        $this->tail = $node;
    }

    public function pop(): mixed
    {
        if ($this->len <= 0 || $this->tail == null) {
            return null;
        }

        $this->len--;

        $tail = $this->tail;
        $this->tail = $tail->prev;
        $tail->prev = null;

        if ($this->len == 0) {
            $this->tail = null;
        }

        return $tail->value;
    }
}
