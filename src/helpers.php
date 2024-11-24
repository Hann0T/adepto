<?php

use Adepto\Facades\View;

if (!function_exists('dd')) {
    function dd(...$args)
    {
        echo '<pre style="background: #f4f4f4; border: 1px solid #ddd; border-left: 3px solid #f36d33; color: #666; page-break-inside: avoid; font-family: monospace; font-size: 15px; line-height: 1.6; margin-bottom: 1.6em; max-width: 100%; overflow: auto; padding: 1em 1.5em; display: block; word-wrap: break-word;">';
        call_user_func_array('var_dump', $args);
        echo '</pre>';
        die();
    }
}

if (!function_exists('view')) {
    function view($view)
    {
        return View::render($view);
    }
}
