<?php

namespace Adepto\Foundation;

use Adepto\Http\Request;
use Closure;

class Application
{
    protected static $instance;

    protected array $bindings = [];
    protected array $instances = [];

    protected array $boostrapers = [
        \Adepto\Foundation\Boostrap\RegisterFacades::class,
    ];

    public static function getInstance(): Application
    {
        return static::$instance ??= new static;
    }

    // https://github.com/laravel/framework/blob/abc1faa60887cb54b4050277e07f0e4f25244a5f/src/Illuminate/Foundation/Application.php#L1590
    // In laravel you need to have bindings, then you can Alias multiple strings or name class 
    // and laravel will try to resolve one of those aliases from the bindings into a concrete object 
    // so, for instance an alias:
    // ['url' => ['Illuminate\Routing\UrlGenerator', 'Illuminate\Contracts\Routing\UrlGenerator']]
    // if you call something like app('url') laravel will find in the bindings and resolve to the concrete, the binding is with 'url'
    // if you call app(\Illuminate\Routing\UrlGenerator) laravel will find first the alias which is 'url' and then resolve the binding
    // the same if you call app(\Illuminate\Contracts\Routing\UrlGenerator)
    public function bootstrap()
    {
        // add service providers
        foreach ($this->boostrapers as $boostraper) {
            $instance = new $boostraper;
            $instance->boostrap($this);
        }
    }

    public function handleRequest(Request $request)
    {
        $this->bind('request', fn () => $request);
    }

    public function singleton(string $abstract, Closure $concrete = null)
    {
        $this->bind($abstract, $concrete, true);
    }

    public function bind(string $abstract, Closure $concrete, bool $singleton = false): void
    {
        unset($this->bindings[$abstract]);
        $this->bindings[$abstract] = compact('concrete', 'singleton');
    }

    public function make(string $abstract, array $params = []): mixed
    {
        return $this->getConcrete($abstract);
    }

    public function getConcrete(string $abstract): mixed
    {
        if (!isset($this->bindings[$abstract])) {
            return $abstract;
        }

        $isSingleton = $this->bindings[$abstract]['singleton'];

        if (!$this->instances[$abstract]) {
            $instance = $this->bindings[$abstract]['concrete']();
            if ($isSingleton) {
                $this->instances[$abstract] = $instance;
            }
        } else {
            $instance = $this->instances[$abstract];
        }
        return $instance;
    }

    public function getBindings(): array
    {
        return $this->bindings;
    }
}
