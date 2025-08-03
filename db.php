<?php
$host = 'localhost';
$dbname = 'blog-app';
$username = 'admin_tawamure';
$password = 'Ah9sD5nqKaAPLR4w';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    exit;
}
