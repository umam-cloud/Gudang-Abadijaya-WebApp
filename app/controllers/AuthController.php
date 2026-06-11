<?php
class AuthController {
    public function login() {
        // If already logged in, redirect to dashboard
        if (isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "dashboard");
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
                header("Location: " . BASE_URL . "dashboard");
                exit;
            } else {
                $error = "Username atau Password salah!";
            }
        }

        require_once __DIR__ . '/../views/auth/login.php';
    }



    public function logout() {
        session_destroy();
        header("Location: " . BASE_URL . "auth/login?msg=success_logout");
        exit;
    }
}
