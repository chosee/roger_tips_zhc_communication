<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use ZHC\Config\Config;
use ZHC\Config\Database;

// Initialize configuration
Config::init();

// Start session
session_start();

// Basic routing
$request = $_SERVER['REQUEST_URI'];
$basePath = dirname($_SERVER['SCRIPT_NAME']);
$path = str_replace($basePath, '', $request);
$path = parse_url($path, PHP_URL_PATH);

// Define routes
$routes = [
    '/' => 'HomeController@index',
    '/login' => 'AuthController@login',
    '/logout' => 'AuthController@logout',
    '/articles' => 'ArticleController@index',
    '/articles/create' => 'ArticleController@create',
    '/articles/store' => 'ArticleController@store',
    '/articles/edit' => 'ArticleController@edit',
    '/articles/update' => 'ArticleController@update',
    '/articles/submit' => 'ArticleController@submit',
    '/articles/process' => 'ArticleController@process',
    '/articles/publish' => 'ArticleController@publish',
    '/articles/reject' => 'ArticleController@reject',
    '/articles/upload-image' => 'ArticleController@uploadImage',
    '/articles/delete-image' => 'ArticleController@deleteImage',
];

// Route the request
if (array_key_exists($path, $routes)) {
    list($controller, $method) = explode('@', $routes[$path]);
    $controllerClass = "ZHC\\Controllers\\$controller";
    
    if (class_exists($controllerClass)) {
        $controller = new $controllerClass();
        if (method_exists($controller, $method)) {
            $controller->$method();
            exit;
        }
    }
}

// If no route matches, show 404 error
header("HTTP/1.0 404 Not Found");
include __DIR__ . '/../views/404.php'; 