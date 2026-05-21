<?php
require_once __DIR__ . '/../config/Database.php';

class UserModel {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function login($username, $password) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    public function register($username, $password, $nama_lengkap) {
        // Check if username already exists
        $stmt = $this->db->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            throw new Exception("Username sudah digunakan!");
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO users (username, password, nama_lengkap) VALUES (?, ?, ?)");
        return $stmt->execute([$username, $hashed_password, $nama_lengkap]);
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT id, username, nama_lengkap, created_at FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
