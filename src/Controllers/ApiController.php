<?php
namespace App\Controllers;

use App\Models\News;
use App\Models\NewsSite;

class ApiController {
    public function getAllNews() {
        $news = News::all();
        header('Content-Type: application/json');
        echo json_encode($news);
    }

    public function getNewsBySite($siteId) {
        $news = News::findBySite($siteId);
        header('Content-Type: application/json');
        echo json_encode($news);
    }
}
