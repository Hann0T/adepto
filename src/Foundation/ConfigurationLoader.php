<?php

namespace Adepto\Foundation;

use Adepto\Support\Config\Repository as Config;
use Adepto\Support\ServiceProvider;

class ConfigurationLoader implements ServiceProvider
{
    public function boot(Application $app): void
    {
        $path = $app->path() . '/../config';
        $files = array_diff(scandir($path), array('.', '..'));

        $items = [];

        foreach ($files as $file) {
            $content = require "{$path}/{$file}";
            $id = str_replace('.php', '', $file);
            $items[$id] = $content;
        }

        $app->singleton('config', function () use ($items) {
            return new Config($items);
        });
    }
}
