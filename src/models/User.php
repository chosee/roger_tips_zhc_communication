<?php

namespace ZHC\Models;

use ZHC\Config\Database;

class User {
    private $db;
    private $id;
    private $username;
    private $email;
    private $role;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function authenticate($username, $password) {
        $stmt = $this->db->prepare('SELECT id, username, password, email, role FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $this->id = $user['id'];
            $this->username = $user['username'];
            $this->email = $user['email'];
            $this->role = $user['role'];
            return true;
        }

        return false;
    }

    public function create($username, $password, $email, $role = 'user') {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        try {
            $stmt = $this->db->prepare('INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)');
            return $stmt->execute([$username, $hashedPassword, $email, $role]);
        } catch (\PDOException $e) {
            // Handle unique constraint violations
            if ($e->getCode() == 23000) {
                return false;
            }
            throw $e;
        }
    }

    public function isAdmin() {
        return $this->role === 'admin';
    }

    public function isEditor() {
        return $this->role === 'editor' || $this->isAdmin();
    }

    public function getId() {
        return $this->id;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getRole() {
        return $this->role;
    }
} 