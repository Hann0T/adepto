<?php

namespace Adepto\Support\Config;

class Repository
{
    public function __construct(protected array $items)
    {
        //
    }

    public function get(string $key, string $default = ''): mixed
    {
        if (str_contains($key, ".")) {
            $exploded = explode(".", $key);
            $key = $exploded[0];
            $subKey = $exploded[1];
            return $this->items[$key][$subKey] ?? ($default ?? null);
        }

        return $this->items[$key] ?? ($default ?? null);
    }

    public function has(string $key): bool
    {
        if (str_contains($key, ".")) {
            $exploded = explode(".", $key);
            $key = $exploded[0];
            $subKey = $exploded[1];
            return isset($this->items[$key][$subKey]);
        }

        return isset($this->items[$key]);
    }

    // TODO: set
}
