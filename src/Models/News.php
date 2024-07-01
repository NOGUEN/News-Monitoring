<?php
namespace App\Models;

class News {
    public static function all() {
        $db = db_connect();
        $stmt = $db->query('SELECT * FROM news ORDER BY published_at DESC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find($id) {
        $db = db_connect();
        $stmt = $db->prepare('SELECT * FROM news WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function findBySite($siteId) {
        $db = db_connect();
        $stmt = $db->prepare('SELECT * FROM news WHERE site_id = ? ORDER BY published_at DESC');
        $stmt->execute([$siteId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function insert($siteId, $title, $link, $category, $publishedAt) {
        $db = db_connect();
        $stmt = $db->prepare('INSERT INTO news (site_id, title, link, category, published_at) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$siteId, $title, $link, $category, $publishedAt]);
    }
}
