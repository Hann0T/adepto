<?php

namespace Adepto;

class UserController
{
    public function get($id)
    {
        return 'user with ID: ' . $id;
    }

    public function post()
    {
        return response()->json([
            'user' => ['name' => 'name of the user']
        ], 200);
    }
}
