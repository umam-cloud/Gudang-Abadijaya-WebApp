<?php
class AuthController {
    public function login() {
        // If already logged in, redirect to dashboard
        if (isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=dashboard&action=index");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username']);
            $password = $_POST['password'];

            $userModel = new UserModel();
            $user = $userModel->login($username, $password);

            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['nama_lengkap'];
                header("Location: index.php?controller=dashboard&action=index");
                exit;
            } else {
                $error = "Username atau Password salah!";
            }
        }

        require_once __DIR__ . '/../views/auth/login.php';
    }

    public function register() {
        // If already logged in, redirect to dashboard
        if (isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=dashboard&action=index");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username']);
            $password = $_POST['password'];
            $nama_lengkap = trim($_POST['nama_lengkap']);

            $userModel = new UserModel();
            try {
                if (strlen($password) < 6) {
                    throw new Exception("Password minimal 6 karakter!");
                }
                
                $userModel->register($username, $password, $nama_lengkap);
                header("Location: index.php?controller=auth&action=login&msg=success_register");
                exit;
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }

        require_once __DIR__ . '/../views/auth/register.php';
    }

    public function logout() {
        session_destroy();
        header("Location: index.php?controller=auth&action=login&msg=success_logout");
        exit;
    }
}
