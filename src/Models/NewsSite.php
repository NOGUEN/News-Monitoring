<?php
namespace App\Models;

use PDO;

class NewsSite {
    public static function getAll() {
        $db = db_connect();
        $stmt = $db->query("SELECT * FROM news_sites ORDER BY name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find($id) {
        $db = db_connect();
        $stmt = $db->prepare('SELECT * FROM news_sites WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
