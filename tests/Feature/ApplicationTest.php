<?php

namespace Tests\Feature;

use Adepto\Foundation\Application;

// TODO: move to other file
class TestHelper
{
    public function __construct(public int $num = 0)
    {
        //
    }
}

class TestDependency
{
    public function __construct(public TestHelper $helper)
    {
        //
    }
}

class TestNestedDependency
{
    public string $name = 'nested dependency';

    public function __construct(public TestDependency $dependency)
    {
        //
    }
}

class TestNestedDependencyWithParameters
{
    public function __construct(public TestDependency $dependency, public $name)
    {
        //
    }
}

class TestNestedDependencyWithParametersUnordered
{
    public function __construct(public string $name, public TestDependency $dependency, public int $num)
    {
        //
    }
}

test('app binding should resolve', function () {
    $app = Application::getInstance();
    $app->bind('helper', fn () => new TestHelper);

    expect($app->make('helper'))->toBeInstanceOf(TestHelper::class);
});

test('app binding should not resolve with the same object', function () {
    $app = Application::getInstance();
    $app->bind('helper', fn () => new TestHelper);

    $firstInstance = $app->make('helper');
    $secondInstance = $app->make('helper');

    expect($firstInstance)->not->toBe($secondInstance);
});

test('app singleton should resolve with the same object', function () {
    $app = Application::getInstance();
    $app->singleton('helper', fn () => new TestHelper);

    $firstInstance = $app->make('helper');
    $secondInstance = $app->make('helper');

    expect($firstInstance)->toBe($secondInstance);
});

test('app should resolve a dependency', function () {
    $app = Application::getInstance();
    $instance = $app->make(TestDependency::class);

    expect($instance->helper)->toBeInstanceOf(TestHelper::class);
    expect($instance->helper)->not->toBeInstanceOf(TestDependency::class);
});

test('app should resolve nested dependencies', function () {
    $app = Application::getInstance();

    $instance = $app->make(TestNestedDependency::class);

    expect($instance)->toBeInstanceOf(TestNestedDependency::class);
    expect($instance->dependency)->toBeInstanceOf(TestDependency::class);
    expect($instance->dependency->helper)->toBeInstanceOf(TestHelper::class);
});

test('app should resolve nested dependencies with parameters', function () {
    $app = Application::getInstance();

    $instance = $app->make(TestNestedDependencyWithParameters::class, ['hans']);

    expect($instance)->toBeInstanceOf(TestNestedDependencyWithParameters::class);
    expect($instance?->name)->toBe("hans");
    expect($instance?->name)->not->toBe("whatever");
    expect($instance->dependency)->toBeInstanceOf(TestDependency::class);
    expect($instance->dependency->helper)->toBeInstanceOf(TestHelper::class);
});

// TODO: more specific tests
test('app should resolve dependencies in whatever order with parameters', function () {
    $app = Application::getInstance();

    $instance = $app->make(TestNestedDependencyWithParametersUnordered::class, ['hans', 7]);

    expect($instance)->toBeInstanceOf(TestNestedDependencyWithParametersUnordered::class);
    expect($instance?->name)->toBe("hans");
    expect($instance?->num)->toBe(7);
    expect($instance?->name)->not->toBe("whatever");
    expect($instance->dependency)->toBeInstanceOf(TestDependency::class);
    expect($instance->dependency->helper)->toBeInstanceOf(TestHelper::class);
});

test('app should resolve closures', function () {
    $app = Application::getInstance();

    $concrete = $app->call(function () {
        return 'jeje';
    });

    expect($concrete)->toBe("jeje");
});

test('app should resolve closures with dependencies', function () {
    $app = Application::getInstance();

    $instance = $app->call(function (TestHelper $dependency) {
        $dependency->num = 10;
        return $dependency;
    });

    expect($instance)->toBeInstanceOf(TestHelper::class);
    expect($instance->num)->toBe(10);
});

test('app should resolve closures with nested dependencies', function () {
    $app = Application::getInstance();

    $instance = $app->call(function (TestNestedDependency $dependency) {
        $dependency->name = 'nested';
        return $dependency;
    });

    expect($instance)->toBeInstanceOf(TestNestedDependency::class);
    expect($instance->name)->toBe('nested');
    expect($instance->dependency)->toBeInstanceOf(TestDependency::class);
    expect($instance->dependency->helper)->toBeInstanceOf(TestHelper::class);
});
