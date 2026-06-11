<?php
// Start Session if needed for flash messages
session_start();

// Define BASE_URL dynamically to prevent CSS/JS missing when trailing slash is omitted
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$base_dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
if ($base_dir === '/') $base_dir = '';
define('BASE_URL', $protocol . "://" . $_SERVER['HTTP_HOST'] . $base_dir . "/");

// Autoloader for MVC components
spl_autoload_register(function ($class_name) {
    $dirs = [
        __DIR__ . '/app/config/',
        __DIR__ . '/app/models/',
        __DIR__ . '/app/controllers/'
    ];
    
    foreach ($dirs as $dir) {
        $file = $dir . $class_name . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Routing
$url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : '';
$url = filter_var($url, FILTER_SANITIZE_URL);
$urlParts = explode('/', $url);

$controllerParam = !empty($urlParts[0]) ? $urlParts[0] : 'dashboard';
$actionParam = !empty($urlParts[1]) ? $urlParts[1] : 'index';

// Authentication Middleware
// If user is not logged in and not trying to access the Auth controller, redirect to login
if (!isset($_SESSION['user_id']) && strtolower($controllerParam) !== 'auth') {
    header("Location: " . BASE_URL . "auth/login");
    exit;
}

// Format controller class name (e.g. "dashboard" -> "DashboardController")
$controllerName = ucfirst(strtolower($controllerParam)) . 'Controller';
$actionName = strtolower($actionParam);

// Dispatch
if (class_exists($controllerName)) {
    $controllerInstance = new $controllerName();
    if (method_exists($controllerInstance, $actionName)) {
        $controllerInstance->$actionName();
    } else {
        // Method not found
        header("HTTP/1.0 404 Not Found");
        echo "404 - Action Not Found";
    }
} else {
    // Controller class not found
    header("HTTP/1.0 404 Not Found");
    echo "404 - Page Not Found";
}
