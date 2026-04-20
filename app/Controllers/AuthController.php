<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Services\AuthService;

class AuthController {
    protected AuthService $authService;

    public function __construct() {
        $this->authService = new AuthService();
    }

    public function register(Request $request) {
        $data = $request->getBody();
        
        if (empty($data['username']) || empty($data['email']) || empty($data['password'])) {
            return Response::error("Missing required fields.", 400);
        }

        try {
            $userId = $this->authService->register($data);
            return Response::success(['user_id' => $userId], "User registered successfully.");
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), 400);
        }
    }

    public function login(Request $request) {
        $data = $request->getBody();
        
        if (empty($data['email']) || empty($data['password'])) {
            return Response::error("Email and password are required.", 400);
        }

        try {
            $result = $this->authService->login($data['email'], $data['password']);
            return Response::success($result, "Login successful.");
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), 401);
        }
    }

    public function logout(Request $request) {
        $body = $request->getBody();
        $user = $body['user_auth'] ?? null;

        if (!$user) {
            return Response::error("User not found.", 404);
        }

        try {
            $this->authService->logout((int)$user['user_id']);
            return Response::success([], "Logged out successfully.");
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), 500);
        }
    }
}
