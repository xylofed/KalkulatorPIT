<?php
$host = '127.0.0.1';
$port = '3306';
$dbname = 'pit_calculator';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;port=$port", $user, $pass);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    echo "✔ Baza danych `$dbname` została utworzona (lub już istniała).\n";
} catch (PDOException $e) {
    echo "❌ Błąd podczas tworzenia bazy danych: " . $e->getMessage() . "\n";
    exit(1);
}
