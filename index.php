<?php

// ----------------------------------------------------
// 1. SETUP HEADER API
// ----------------------------------------------------
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Khusus untuk permintaan OPTIONS (preflight CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// ----------------------------------------------------
// 2. INCLUDE CONTROLLER (HARUS SEBELUM INSTANSIASI!)
// ----------------------------------------------------
// Pastikan path ini benar dan kapitalisasi folder 'Controllers' sesuai
include_once 'src/Controllers/UserController.php'; 

// ----------------------------------------------------
// 3. LOGIKA ROUTER
// ----------------------------------------------------

// Mendapatkan method HTTP
$method = $_SERVER['REQUEST_METHOD'];

// Mendapatkan URI (Uniform Resource Identifier)
$uri = $_SERVER['REQUEST_URI'];
$uri = preg_replace('/(\?.+)/', '', $uri); // Hapus query string jika ada

// 🛠️ PERBAIKAN KRITIS: HAPUS BASE PATH PROYEK
// GANTI '/ENDPOINTCRUD' dengan nama folder proyek Anda di localhost (Laragon/XAMPP)
$base_path = '/ENDPOINTCRUD'; 
if (strpos($uri, $base_path) === 0) {
    $uri = substr($uri, strlen($base_path));
}
// -----------------------------------------------------

$uri = trim($uri, '/'); // Membersihkan slash di awal/akhir

// Buat instance Controller dan tangani request
// Baris ini sekarang aman karena UserController sudah di-include di atas
$controller = new UserController(); 
$controller->handleRequest($method, $uri);

?>