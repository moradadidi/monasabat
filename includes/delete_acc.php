<?php
include_once("../includes/database.php");
session_start();

if (isset($_SESSION['id_user'])) {
    $id_user = $_SESSION['id_user'];
    $user = $pdo->prepare("DELETE FROM users WHERE id_user = ?");
    
    try {
        $user->execute([$id_user]);
    } catch (PDOException $e) {
        // Log the error if needed
        error_log("Error deleting user: " . $e->getMessage());
        // Redirect to an error page or show an error message
        header("Location: /error.php");
        exit();
    }

    session_destroy();
    header("Location: ../public/login.php");
    exit();
} else {
    // If no user is logged in, redirect to the login page
    header("Location:  ../public/login.php");
    exit();
}
?>
