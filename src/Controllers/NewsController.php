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
                'news' => $newsSites
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
            $news = News::getNewsBySite($siteId);
            echo json_encode([
                'news' => $news
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
                'news' => $newsSites
            ]);
        } catch (\Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getPagedNews($params) {
        header('Content-Type: application/json');

        try {
            $limit = isset($params['limit']) ? (int)$params['limit'] : 10;
            $page = isset($params['page']) ? (int)$params['page'] : 1;
            $offset = ($page - 1) * $limit;
    
            $news = News::getPagedNews($limit, $offset);
            echo json_encode([
                'news' => $news
            ]);
        } catch (\Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getPagedNewsBySiteId($params) {
        header('Content-Type: application/json');

        try {
            $siteId = isset($params['siteId']) ? (int)$params['siteId'] : 1;
            $limit = isset($params['limit']) ? (int)$params['limit'] : 10;
            $page = isset($params['page']) ? (int)$params['page'] : 1;
            $offset = ($page - 1) * $limit;
    
            $news = News::getPagedNewsBySiteId($siteId, $limit, $offset);
            echo json_encode([
                'news' => $news
            ]);
        } catch (\Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getNewsPageCount($limit) {
        $totalNews = News::countNews();
        $totalPages = ceil($totalNews / $limit);

        header('Content-Type: application/json');
        echo json_encode(['total_pages' => $totalPages]);
    }

    public function getNewsPageCountBySite($params) {
        $siteId = isset($params['siteId']) ? (int)$params['siteId'] : 1;
        $limit = isset($params['limit']) ? (int)$params['limit'] : 10;
        $totalNews = News::countNewsBySiteId($siteId);
        $totalPages = ceil($totalNews / $limit);

        header('Content-Type: application/json');
        echo json_encode(['total_pages' => $totalPages]);
    }
}
