<?php
namespace App\Controllers;

use App\Models\News;
use App\Models\NewsSite;

class NewsController {
    public function index() {
        header('Content-Type: application/json');

        try {
            $newsSites = NewsSite::getAll();

            echo json_encode([
                'status' => 'success',
                'data' => $newsSites
            ]);
        } catch (\Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getNewsBySite($siteId) {
        header('Content-Type: application/json');

        try {
            $news = News::findBySite($siteId);
            echo json_encode([
                'status' => 'success',
                'data' => $news
            ]);
        } catch (\Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getAllNews() {
        header('Content-Type: application/json');

        try {
            $newsSites = News::getAll();
            echo json_encode([
                'status' => 'success',
                'data' => $newsSites
            ]);
        } catch (\Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
