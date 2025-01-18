<?php

namespace Adepto;

use Adepto\Http\Request;
use Adepto\Http\Response;
use Closure;

class RejectMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // if you register this middleware twice in the same route
        // the user will be redirect to /user/1
        if ($request->header('redirect') == 'true') {
            return redirect('/user/1');
        }

        $request->setHeader('redirect', 'true');

        return $next($request);
    }
}
