<?php

namespace ZHC\Models;

use ZHC\Config\Database;
use ZHC\Config\Config;

class Article {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($title, $content, $authorId) {
        $this->db->beginTransaction();

        try {
            $stmt = $this->db->prepare('
                INSERT INTO news_articles (title, content, author_id, status)
                VALUES (?, ?, ?, "draft")
            ');
            $stmt->execute([$title, $content, $authorId]);
            $articleId = $this->db->lastInsertId();

            $this->db->commit();
            return $articleId;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function update($id, $title, $content) {
        $stmt = $this->db->prepare('
            UPDATE news_articles
            SET title = ?, content = ?, updated_at = CURRENT_TIMESTAMP
            WHERE id = ?
        ');
        return $stmt->execute([$title, $content, $id]);
    }

    public function submitForReview($id) {
        $stmt = $this->db->prepare('
            UPDATE news_articles
            SET status = "pending"
            WHERE id = ? AND status = "draft"
        ');
        return $stmt->execute([$id]);
    }

    public function startProcessing($id, $processorId) {
        $stmt = $this->db->prepare('
            UPDATE news_articles
            SET status = "processing", processor_id = ?
            WHERE id = ? AND status = "pending"
        ');
        return $stmt->execute([$processorId, $id]);
    }

    public function publish($id, $platforms = ['website', 'agenda', 'mobile_app']) {
        $this->db->beginTransaction();

        try {
            // Update article status
            $stmt = $this->db->prepare('
                UPDATE news_articles
                SET status = "published", published_at = CURRENT_TIMESTAMP
                WHERE id = ?
            ');
            $stmt->execute([$id]);

            // Create publication records
            $stmt = $this->db->prepare('
                INSERT INTO publication_history (article_id, platform, status)
                VALUES (?, ?, "pending")
            ');

            foreach ($platforms as $platform) {
                $stmt->execute([$id, $platform]);
            }

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function reject($id, $processorId) {
        $stmt = $this->db->prepare('
            UPDATE news_articles
            SET status = "rejected", processor_id = ?
            WHERE id = ?
        ');
        return $stmt->execute([$processorId, $id]);
    }

    public function getPendingArticles() {
        $stmt = $this->db->prepare('
            SELECT a.*, u.username as author_name
            FROM news_articles a
            JOIN users u ON a.author_id = u.id
            WHERE a.status = "pending"
            ORDER BY a.created_at DESC
        ');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getArticleById($id) {
        $stmt = $this->db->prepare('
            SELECT a.*, u.username as author_name,
                   p.username as processor_name
            FROM news_articles a
            JOIN users u ON a.author_id = u.id
            LEFT JOIN users p ON a.processor_id = p.id
            WHERE a.id = ?
        ');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getArticlesByAuthor($authorId) {
        $stmt = $this->db->prepare('
            SELECT a.*, u.username as author_name,
                   p.username as processor_name
            FROM news_articles a
            JOIN users u ON a.author_id = u.id
            LEFT JOIN users p ON a.processor_id = p.id
            WHERE a.author_id = ?
            ORDER BY a.created_at DESC
        ');
        $stmt->execute([$authorId]);
        return $stmt->fetchAll();
    }

    public function addImage($articleId, $originalFilename, $filename, $fileSize, $mimeType) {
        $stmt = $this->db->prepare('
            INSERT INTO article_images (article_id, filename, original_filename, file_size, mime_type)
            VALUES (?, ?, ?, ?, ?)
        ');
        return $stmt->execute([$articleId, $filename, $originalFilename, $fileSize, $mimeType]);
    }

    public function getImages($articleId) {
        $stmt = $this->db->prepare('
            SELECT * FROM article_images
            WHERE article_id = ?
            ORDER BY created_at ASC
        ');
        $stmt->execute([$articleId]);
        return $stmt->fetchAll();
    }

    public function deleteImage($imageId, $articleId) {
        // First get the filename to delete the actual file
        $stmt = $this->db->prepare('
            SELECT filename FROM article_images
            WHERE id = ? AND article_id = ?
        ');
        $stmt->execute([$imageId, $articleId]);
        $image = $stmt->fetch();

        if ($image) {
            $uploadPath = Config::get('upload.path');
            $filePath = $uploadPath . '/' . $image['filename'];
            
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            $stmt = $this->db->prepare('
                DELETE FROM article_images
                WHERE id = ? AND article_id = ?
            ');
            return $stmt->execute([$imageId, $articleId]);
        }

        return false;
    }
} 