<?php
session_start();
include "./database/db_connect.php";

$message = "";

if (!isset($_SESSION["login_token"])) {
    $message = "User isn't logged in.";
    echo $message;
    exit();
}

$loginToken = $_SESSION["login_token"];

$stmt = $conn->prepare("SELECT id FROM users WHERE login_token = ?");
if (!$stmt) {
    $message = "Database error. Please try again later.";
    echo $message;
    exit();
}

$stmt->bind_param("s", $loginToken);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    $message = "Login is incorrect. Please log in again.";
    $stmt->close();
    echo $message;
    exit();
}

$stmt->bind_result($userId);
$stmt->fetch();
$stmt->close();

$_SESSION = [];
session_destroy();

// Redirect
header("Location: /");
exit();
