<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Services\ProgressService;

class ProgressController {
    protected ProgressService $progressService;

    public function __construct() {
        $this->progressService = new ProgressService();
    }

    public function index(Request $request) {
        $userId = $request->getBody()['user_id'] ?? null;
        if (!$userId) {
            return Response::error("User ID is required.", 400);
        }

        try {
            $progress = $this->progressService->getProgress((int)$userId);
            if (!$progress) {
                return Response::error("Progress not found.", 404);
            }
            return Response::success($progress, "Progress fetched successfully.");
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), 500);
        }
    }

    public function update(Request $request) {
        $data = $request->getBody();
        $userId = $data['user_id'] ?? null;
        if (!$userId) {
            return Response::error("User ID is required.", 400);
        }

        try {
            $this->progressService->updateProgress((int)$userId, $data);
            $newProgress = $this->progressService->getProgress((int)$userId);
            return Response::success($newProgress, "Progress updated successfully.");
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), 400);
        }
    }
}
