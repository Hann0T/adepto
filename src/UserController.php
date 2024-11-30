<?php

namespace Adepto;

use Adepto\Http\Request;

class UserController
{
    public function get(Request $request, $id)
    {
        return  $request->method() . ' ' . 'user with ID: ' . $id;
    }

    public function show()
    {
        return 'all users';
    }

    public function post()
    {
        return response()->json([
            'user' => ['name' => 'name of the user']
        ], 200);
    }
}
