<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=monasabat', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die('PDO connection error -- ' . $e->getMessage());
}
?>