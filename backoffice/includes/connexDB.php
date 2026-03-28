<?php
//fonction de connexion à la base de données
$defaultHost = file_exists('/.dockerenv') ? 'db' : 'localhost';
$host = getenv('DB_HOST');
if (empty($host) && isset($_SERVER['DB_HOST'])) {
    $host = $_SERVER['DB_HOST'];
}
if (empty($host)) {
    $host = $defaultHost;
}
$db   = getenv('DB_NAME') ?: 'iran_war';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS');
if ($pass === false) {
    $pass = '';
}
$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}