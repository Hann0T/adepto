<?php

namespace Adepto;

use Adepto\Http\Response;

class View
{
    protected string $directory = __DIR__;

    public function render(string $view, array $params = []): Response
    {
        $filePath = "{$this->directory}/views/{$view}.php";

        if (file_exists($filePath)) {
            ob_start();
            include_once $filePath;
            $rawView = ob_get_clean();

            // FIX:
            // $rawView = htmlspecialchars($rawView);

            // eval variables
            $rawView = preg_replace_callback('/\{\{\s*\$([a-zA-Z0-9]+)\s*\}\}/', function ($match) use ($params) {
                $key  = $match[1];
                return $params[$key] ?? 'undefined';
            }, $rawView);

            $rawView = preg_replace_callback('/\{\{\s*([a-zA-Z0-9_]+)\(\s*(.*)\)\s*\}\}/', function ($match) {
                $function  = trim($match[1]);
                $args = str_replace(', ', ',', $match[2]);

                // is array
                if (preg_match('/\[(.*)\]/', $args)) {
                    $args = str_replace("'", "\"", $args);
                    $args = json_decode($args);
                    $args = [$args];
                } else {
                    $args = str_replace('\'', '', $args);
                    $args = str_replace('"', '', $args);
                    $args = explode(',', trim($args));
                }

                if (function_exists($function)) {
                    return call_user_func_array($function, $args);
                }

                return 'undefined';
            }, $rawView);

            return new Response($rawView, 200);
        } else {
            throw new \Error('View does not exists.');
        }
    }
}
