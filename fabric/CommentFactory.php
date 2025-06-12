<?php
require_once __DIR__ . '/../Model/Comment.php';
require_once __DIR__ . '/../functions/Database.php';

class CommentFactory {
    public static function create(): Comment {
        $db = new Database();
        return new Comment($db);
    }
}
