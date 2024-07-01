<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$config = [
    'db' => [
        'host' => $_ENV['DB_HOST'],
        'user' => $_ENV['DB_USER'],
        'pass' => $_ENV['DB_PASS'],
        'name' => $_ENV['DB_NAME'],
    ],
];

function db_connect() {
    global $config;
    $db = new PDO('mysql:host=' . $config['db']['host'] . ';dbname=' . $config['db']['name'],
                  $config['db']['user'], $config['db']['pass']);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $db;
}

function fetchAndStoreNews() {
    $db = db_connect();

    $stmt = $db->query('SELECT id, name, url FROM news_sites');
    $newsSites = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $today = new DateTime();
    $today->setTime(0, 0);

    foreach ($newsSites as $site) {
        $rss = simplexml_load_file($site['url']);

        if ($rss) {
            foreach ($rss->channel->item as $item) {
                
                $title = (string) $item->title;
                $link = (string) $item->link;
                $published_at = date('Y-m-d H:i:s', strtotime((string) $item->pubDate));

                
                $stmt = $db->prepare('INSERT INTO news (site_id, title, link, published_at) VALUES (?, ?, ?, ?)');
                $stmt->execute([$site['id'], $title, $link, $published_at]);
                
            }
            echo "News data fetched and stored successfully.\n";
        } else {
            echo "Failed to load RSS feed from: " . $site['url'];
        }
    }
}

fetchAndStoreNews();


?>
