<?php 
session_start();
require 'auth.php';
require 'db.php'; 

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
$stmt->execute([$id]);

header("Location: index.php");

