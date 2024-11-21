<?php

namespace Adepto\Foundation;

use Closure;

class Application
{
    protected array $bindings = [];

    protected array $boostrapers = [
        \Adepto\Foundation\Boostrap\RegisterFacades::class,
    ];

    public function bootstrap()
    {
        foreach ($this->boostrapers as $boostraper) {
            $instance = new $boostraper;
            $instance->boostrap($this);
        }
    }

    public function bind(string $abstract, Closure $concrete): void
    {
        unset($this->bindings[$abstract]);
        $this->bindings[$abstract] = $concrete();
    }

    public function getConcrete(string $abstract): mixed
    {
        if (isset($this->bindings[$abstract])) {
            return $this->bindings[$abstract];
        }

        return $abstract;
    }

    public function getBindings(): array
    {
        return $this->bindings;
    }
}
