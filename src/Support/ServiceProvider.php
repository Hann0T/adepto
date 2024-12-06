<?php

namespace Adepto\Support;

use Adepto\Foundation\Application;

interface ServiceProvider
{
    public function boot(Application $app): void;
}
