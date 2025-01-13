<?php

namespace Adepto;

use Adepto\Bus\Contracts\Queue\ShouldQueue;
use Adepto\Http\Request;

class StoreUserJob implements ShouldQueue
{
    public function __construct()
    {
        //
    }

    public function handle(Request $request)
    {
        // 
    }
}
