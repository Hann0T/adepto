<?php

namespace Adepto;

class View
{
    protected string $directory = __DIR__;

    public function render(string $view, array $params = []): string
    {
        $filePath = "{$this->directory}/views/{$view}.php";

        if (file_exists($filePath)) {
            ob_start();
            include_once $filePath;
            return ob_get_clean();
        } else {
            throw new \Error('View does not exists.');
        }
    }
}
