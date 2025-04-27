<?php
session_start();
include "./database/db_connect.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST["username"]) || !isset($_POST["password"])) {
        $message = "Username and password must be provided.";
    } else {
        $username = trim($_POST["username"]);
        $password = $_POST["password"];

        $stmt = $conn->prepare("SELECT username FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = "A user with the same username already exists. Please log in.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $loginToken = bin2hex(random_bytes(32));

            $stmt = $conn->prepare("INSERT INTO users (username, password, login_token) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $hashedPassword, $loginToken);
            $stmt->execute();

            $_SESSION["login_token"] = $loginToken;
            header("Location: /");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/auth.css">
    <link rel="stylesheet" href="/css/main.css">
</head>

<body>
    <div class="container">
        <h1>Register</h1>

        <?php if (!empty($message)): ?>
            <p class="message"><?= htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <form method="POST" action="register">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <div class="bottom">
                <a href="/" class="go-back">Go home</a>
                <a href="login" class="go-back">Already have an account?</a>
            </div>
            <button type="submit">Register</button>
        </form>
    </div>
</body>

</html>