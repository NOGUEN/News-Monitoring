<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../src/routes.php';

use App\controllers\NewsController;

$router = new Router();
$newsController = new NewsController();

$router->get('/', [$newsController, 'index']);
$router->get('/news/{siteId}', [$newsController, 'getNewsBySite']);
$router->get('/news', [$newsController, 'getAllNews']);

$router->dispatchURI($_SERVER['REQUEST_URI']);