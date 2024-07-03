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

$router->get('/news/page/default', function() use ($newsController) {
    $params = [
        'limit' => isset($_GET['limit']) ? $_GET['limit'] : 10,
        'page' => isset($_GET['page']) ? $_GET['page'] : 1,
    ];
    $newsController->getPagedNews($params);
});

$router->get('/news/page/site', function() use ($newsController) {
    $params = [
        'limit' => isset($_GET['limit']) ? $_GET['limit'] : 10,
        'page' => isset($_GET['page']) ? $_GET['page'] : 1,
        'siteId' => isset($_GET['siteId']) ? $_GET['siteId'] : 1
    ];
    $newsController->getPagedNewsBySiteId($params);
});

$router->get('/news/page/count', function() use ($newsController) {
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $newsController->getNewsPageCount($limit);
});

$router->get('/news/page/count/site', function() use ($newsController) {
    $params = [
        'limit' => isset($_GET['limit']) ? $_GET['limit'] : 10,
        'siteId' => isset($_GET['siteId']) ? $_GET['siteId'] : 1
    ];
    $newsController->getNewsPageCountBySite($params);
});

$router->dispatchURI($_SERVER['REQUEST_URI']);