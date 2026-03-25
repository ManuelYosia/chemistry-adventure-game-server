<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;

class TestController {
    public function index(Request $request) {
        return Response::success([
            'method' => $request->getMethod(),
            'path' => $request->getPath(),
            'query' => $_GET
        ], "Test successful.");
    }
}
