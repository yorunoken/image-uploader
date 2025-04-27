<?php
session_start();
include "./database/db_connect.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST["username"]) || !isset($_POST["password"])) {
        $message = "Username and password must be provided.";
    } else {
        $usernameProvided = trim($_POST["username"]);
        $passwordProvided = $_POST["password"];

        $stmt = $conn->prepare("SELECT password, login_token, id FROM users WHERE username = ?");
        if (!$stmt) {
            $message = "Database error. Please try again later.";
        } else {
            $stmt->bind_param("s", $usernameProvided);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows === 0) {
                $message = "Incorrect username or password.";
            } else {
                $stmt->bind_result($correctPasswordHashed, $loginToken, $userId);
                $stmt->fetch();
                $stmt->close();

                if (!password_verify($passwordProvided, $correctPasswordHashed)) {
                    $message = "Incorrect username or password.";
                } else {
                    // Credentials are correct.
                    if (!$loginToken) {
                        $loginToken = bin2hex(random_bytes(32));

                        $stmt = $conn->prepare("UPDATE users SET login_token = ? WHERE id = ?");
                        if (!$stmt) {
                            $message = "Database error. Please try again later.";
                        } else {
                            $stmt->bind_param("si", $loginToken, $userId);
                            $stmt->execute();
                            $stmt->close();
                        }
                    }

                    $_SESSION["login_token"] = $loginToken;
                    $_SESSION['username'] = $usernameProvided;
                    header("Location: /");
                    exit();
                }
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/auth.css">
    <link rel="stylesheet" href="/css/main.css">
</head>

<body>
    <div class="container">
        <div class="terminal-header">
            <div class="terminal-buttons">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <div class="terminal-title">user@imageuploader:~/login</div>
        </div>

        <div class="form-content">
            <h1>Login</h1>

            <?php if (!empty($message)): ?>
                <p class="message"><?= htmlspecialchars($message); ?></p>
            <?php endif; ?>

            <form method="POST" action="login">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>

                <div class="bottom">
                    <a href="/" class="go-back">Go home</a>
                    <a href="register" class="go-back">Don't have an account?</a>
                </div>
                <button type="submit">Login</button>
            </form>
        </div>
    </div>
</body>

</html>