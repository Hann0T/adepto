<?php

namespace Adepto;

use Adepto\Http\Request;
use Adepto\Http\Response;
use Closure;

class AuthMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->cookie('auth') !== 'true') {
            return abort(401, 'unauthorized');
        }

        return $next($request);
    }
}
