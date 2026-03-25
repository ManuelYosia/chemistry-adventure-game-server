<?php

namespace App\Middleware;

use App\Core\Middleware;
use App\Core\Request;
use App\Core\Response;
use App\Repositories\UserRepository;

class AuthMiddleware implements Middleware {
    protected UserRepository $userRepository;

    public function __construct() {
        $this->userRepository = new UserRepository();
    }

    public function handle(Request $request) {
        $token = $this->extractToken($request);

        if (!$token) {
            Response::error("Authentication token required.", 401);
        }

        $user = $this->userRepository->findByToken($token);

        if (!$user) {
            Response::error("Invalid or expired session.", 401);
        }

        // Attach user info to the request for controller access
        $request->setBody(array_merge($request->getBody(), ['user_auth' => $user]));
    }

    protected function extractToken(Request $request) {
        $headers = $request->getHeaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;

        if ($authHeader && preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            return $matches[1];
        }

        return $request->getBody()['token'] ?? null;
    }
}
