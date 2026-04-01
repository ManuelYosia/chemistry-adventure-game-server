<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Services\LevelResultService;

class LevelResultController {
    protected LevelResultService $levelResultService;

    public function __construct() {
        $this->levelResultService = new LevelResultService();
    }

    public function index(Request $request) {
        $userId = $request->getBody()['user_auth']['user_id'];

        try {
            $results = $this->levelResultService->getResults((int)$userId);
            return Response::success($results, "Level results fetched successfully.");
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), 500);
        }
    }

    public function save(Request $request) {
        $data = $request->getBody();
        $data['user_id'] = $data['user_auth']['user_id'];
        $required = ['map_id', 'level_id', 'score', 'stars', 'is_completed'];
        
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                return Response::error("Field '$field' is required.", 400);
            }
        }

        try {
            $this->levelResultService->saveResult($data);
            return Response::success([], "Level result saved successfully.");
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), 400);
        }
    }
}
