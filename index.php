<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

include_once 'src/Controllers/UserController.php'; 

$method = $_SERVER['REQUEST_METHOD'];


$uri = $_SERVER['REQUEST_URI'];
$uri = preg_replace('/(\?.+)/', '', $uri); 

$base_path = '/ENDPOINTCRUD'; 
if (strpos($uri, $base_path) === 0) {
    $uri = substr($uri, strlen($base_path));
}


$uri = trim($uri, '/'); 

$controller = new UserController(); 
$controller->handleRequest($method, $uri);

?>