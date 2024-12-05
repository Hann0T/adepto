<?php

namespace Adepto\Foundation;

use Adepto\Http\Request;
use Adepto\Http\Response;
use Closure;
use ReflectionClass;
use ReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionMethod;
use ReflectionNamedType;

class Application
{
    protected static $instance;

    protected array $bindings = [];
    protected array $instances = [];

    protected array $aliases = [
        'request' => [\Adepto\Http\Request::class]
    ];

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

    public function singleton(string $abstract, Closure $concrete)
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
        return $this->resolve($abstract, $params);
    }

    /**
     * Resolve a Closure|[class, method] with dependencies
     */
    public function call(Closure|array $abstract, array $params = []): mixed
    {
        if ($abstract instanceof Closure) {
            $reflector = new ReflectionFunction($abstract);
            $callback = $abstract;
        } else {
            $class = $abstract[0];
            $method = $abstract[1];
            $controller = $this->make($class);
            $callback = [$controller, $method];
            $reflector = new ReflectionMethod($class, $method);
        }

        $dependencies = $this->resolveDependencies($reflector, $params);
        return call_user_func($callback, ...$dependencies);
    }

    /**
     * Get the concrete type from the bindings or instantiate 
     * a concrete instance with dependencies
     */
    protected function resolve(string $abstract, array $params = [])
    {
        // We try to get the concrete from the bindings
        $concrete = $this->getConcreteFromBindings($abstract);

        // If the concrete is no longer a string then we return the instance.
        // TODO: more validations
        if (!is_string($concrete)) {
            return $concrete;
        }

        return $this->build($concrete, $params);
    }

    /**
     * If the binding is registered then try to get the abstract 
     * from the bindings or create a new instance.
     */
    protected function getConcreteFromBindings(string $abstract): mixed
    {
        // If the $abstract is not a key alias and is not binded
        if (!isset($this->aliases[$abstract]) && !isset($this->bindings[$abstract])) {
            $filtered = array_filter($this->aliases, function ($values, $alias) use ($abstract) {
                if (in_array($abstract, $values)) {
                    return $alias;
                }
            }, ARRAY_FILTER_USE_BOTH);

            if (count($filtered) > 0) {
                $abstract = array_pop(array_keys($filtered));
            }
        }

        if (!isset($this->bindings[$abstract])) {
            return $abstract;
        }

        $isSingleton = $this->bindings[$abstract]['singleton'];

        if (!isset($this->instances[$abstract])) {
            $instance = $this->bindings[$abstract]['concrete']();
            if ($isSingleton) {
                $this->instances[$abstract] = $instance;
            }
        } else {
            $instance = $this->instances[$abstract];
        }

        return $instance;
    }

    /**
     * Instantiate a instance and resolve dependencies
     */
    protected function build(string $concrete, array $params = []): mixed
    {
        if (!class_exists($concrete)) {
            return $concrete;
        }

        $reflector = new ReflectionClass($concrete);
        if (!$reflector->isInstantiable()) {
            throw new \Error("Class [{$concrete}] is not instantiable.");
        }

        $constructor  = $reflector->getConstructor();
        if (!$constructor) {
            return new $concrete;
        }

        $resolvedParams = $this->resolveDependencies($constructor, $params);
        return $reflector->newInstance(...$resolvedParams);
    }

    protected function resolveDependencies(ReflectionFunctionAbstract $reflector, array $params = []): mixed
    {
        $resolvedParams = [];
        foreach ($reflector->getParameters() as $param) {
            $type = $param->getType();

            // if it does not have a type defined
            // we push from the params
            if (!$type instanceof ReflectionNamedType) {
                array_push($resolvedParams, array_shift($params));
                continue;
            }

            if (!$type->isBuiltin()) {
                // get the name of the type and resolve
                // it should be a class name
                if (class_exists($type->getName()) && (new ReflectionClass($type->getName()))->isInstantiable()) {
                    array_push($resolvedParams, $this->make($type->getName()));
                    continue;
                }

                // TODO: Should test this, with middlewares
                // should test a Closure as parameter/dependency
                // Closure is not builtin
                if ($type->getName() == Closure::class) {
                    array_push($resolvedParams, array_shift($params));
                    continue;
                }
            }

            if ($type->allowsNull() && count($params) <= 0) {
                array_push($resolvedParams, null);
                continue;
            }

            if ($param->isOptional() && count($params) <= 0) {
                array_push($resolvedParams, $param->getDefaultValue());
                continue;
            }

            array_push($resolvedParams, array_shift($params));
        }

        return $resolvedParams;
    }

    public function getBindings(): array
    {
        return $this->bindings;
    }

    public function terminate(Response $response)
    {
        print($response->getContent());
        exit();
    }
}
