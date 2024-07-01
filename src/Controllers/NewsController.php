<?php
namespace App\Controllers;

use App\Models\News;
use App\Models\NewsSite;

class NewsController {
    public function index() {
        print("show all index");
    }

    public function show($siteId) {
        print("show all site");
    }
}
