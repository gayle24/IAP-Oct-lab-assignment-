<?php
require_once '../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable('../');
$dotenv->load();


function getDbConnection() {
    $dsn = 'mysql:host=localhost;dbname=php_2fa';
    $username = $_ENV['db_name'];
    $password = $_ENV['db_password'];
 
    return new PDO($dsn, $username, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
}
?>
