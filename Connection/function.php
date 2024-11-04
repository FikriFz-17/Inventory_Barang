<?php 
// Cek apakah session sudah dimulai
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Muat autoload Composer
require_once __DIR__ . '/vendor/autoload.php';

// Inisialisasi dotenv
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Mengambil variabel koneksi database dari .env
$db_host = $_ENV['DB_HOST'];
$db_username = $_ENV['DB_USERNAME'];
$db_password = $_ENV['DB_PASSWORD'];
$db_name = $_ENV['DB_NAME'];

// Koneksi ke database
$conn = mysqli_connect($db_host, $db_username, $db_password, $db_name);

// Cek koneksi
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
