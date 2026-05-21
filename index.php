<?php
// Start Session if needed for flash messages
session_start();

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
$controllerParam = isset($_GET['controller']) ? $_GET['controller'] : 'dashboard';
$actionParam = isset($_GET['action']) ? $_GET['action'] : 'index';

// Authentication Middleware
// If user is not logged in and not trying to access the Auth controller, redirect to login
if (!isset($_SESSION['user_id']) && strtolower($controllerParam) !== 'auth') {
    header("Location: index.php?controller=auth&action=login");
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
