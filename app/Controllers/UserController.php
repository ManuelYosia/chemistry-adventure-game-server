<?php

namespace App\Controllers;

use App\Core\Response;
use App\Core\Request;

class UserController
{
    public function index(Request $request)
    {
        return Response::success([
            'method' => $request->getMethod(),
            'path' => $request->getPath(),
            'query' => $_GET
        ], "User successful.");
    }
}