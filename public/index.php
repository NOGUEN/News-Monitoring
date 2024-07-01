<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../src/routes.php';

use App\controllers\ApiController;
use App\controllers\NewsController;

$router = new Router();
$newsController = new NewsController();
$apiController = new ApiController();

$router->get('/', [$newsController, 'index']);
$router->get('/news/{siteId}', [$newsController, 'show']);
$router->get('/api/news', [$apiController, 'getAllNews']);
$router->get('/api/news/{siteId}', [$apiController, 'getNewsBySite']);

$router->dispatchURI($_SERVER['REQUEST_URI']);