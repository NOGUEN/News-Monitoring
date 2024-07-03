<?php
namespace App\Models;

use PDO;

class News {
    public static function getAll() {
        $db = db_connect();
        $stmt = $db->query('SELECT * FROM news ORDER BY published_at DESC');

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getNewsBySite($siteId) {
        $db = db_connect();
        $stmt = $db->prepare('SELECT * FROM news WHERE site_id = ? ORDER BY published_at DESC');
        $stmt->execute([$siteId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getPagedNews($limit, $offset) {
        $db = db_connect();
        $stmt = $db->prepare('SELECT * FROM news ORDER BY published_at DESC LIMIT :limit OFFSET :offset');
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getPagedNewsBySiteId($siteId, $limit, $offset) {
        $db = db_connect();
        $stmt = $db->prepare('SELECT * FROM news WHERE site_id = :siteId ORDER BY published_at DESC LIMIT :limit OFFSET :offset');
        $stmt->bindParam(':siteId', $siteId, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function countNews() {
        $db = db_connect();
        $stmt = $db->query('SELECT COUNT(*) FROM news');
        
        return $stmt->fetchColumn();
    }

    public static function countNewsBySiteId($siteId) {
        $db = db_connect();
        $stmt = $db->prepare('SELECT COUNT(*) FROM news WHERE site_id = :siteId');
        $stmt->bindParam(':siteId', $siteId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchColumn();
    }
}
