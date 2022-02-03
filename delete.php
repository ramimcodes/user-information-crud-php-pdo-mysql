<?php

$pdo = new PDO('mysql:host=localhost;port=3306;dbname=users-info', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$id = $_POST['id'] ?? null;

if (!$id) {
    header('Location:index.php');
    exit;
}

$result = $pdo->prepare('DELETE FROM users WHERE id = :id');
$result->bindValue(':id', $id);
$result->execute();
header('Location:index.php');
