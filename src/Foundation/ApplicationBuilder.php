<?php

namespace Adepto\Foundation;

use Adepto\Support\AppRouteServiceProvider;
use Adepto\Support\FacadeServiceProvider;
use Adepto\Support\ServiceProvider;

class ApplicationBuilder
{
    protected array $providers = [];

    public function __construct(protected Application $app, protected string $basePath)
    {
        //
    }

    public function create()
    {
        $this->app = Application::getInstance();
        $this->bootstrap();
        return $this->app;
    }

    public function withProviders(array $providers)
    {
        $this->providers = array_merge($this->providers, $providers);
    }

    public function withRouting(string $web = '', string $api = '')
    {
        array_push($this->providers, new AppRouteServiceProvider([$web, $api]));
        return $this;
    }

    protected function bootstrap()
    {
        // First we need to register some Facades
        $this->boostrapFacades();
        $this->bootstrapProviders();
    }

    protected function boostrapFacades()
    {
        (new FacadeServiceProvider())->boot($this->app);
    }

    protected function bootstrapProviders()
    {
        foreach ($this->providers as $provider) {
            if (is_string($provider)) {
                $instance = new $provider;
            } else {
                if (!is_object($provider)) {
                    throw new \Exception("Invalid {$provider} for bootstap.");
                }
                $instance = $provider;
            }

            if (!$instance instanceof ServiceProvider) {
                throw new \Exception("{$instance} is not instance of \Adepto\Support\ServiceProvider::class.");
            }

            $instance->boot($this->app);
        }
    }
}
