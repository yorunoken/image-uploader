<?php
session_start();
include "./database/db_connect.php";
include "./database/utils.php";

// Check if user is logged in
$isLoggedIn = loginTokenIsValid(isset($_SESSION["login_token"]) ? $_SESSION["login_token"] : "");
$userId = $isLoggedIn ? getUserFromLoginToken($_SESSION["login_token"])["id"] : null;

// Setting up variables
$message = "";
$messageType = "";
$maxFileSize = 5 * 1024 * 1024;
$allowedExtensions = ["jpg", "jpeg", "png", "gif"];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["image"])) {
    $file = $_FILES["image"];

    if ($file["error"] !== UPLOAD_ERR_OK) {
        $message = "Upload failed with error code: " . $file["error"];
        $messageType = "error";
    } else {
        if ($file["size"] > $maxFileSize) {
            $message = "File size too large. Maximum size is: " . $maxFileSize . "MB";
            $messageType = "error";
        } else {
            $fileExtension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));

            if (!in_array($fileExtension, $allowedExtensions)) {
                $message = "Only the following file types are allowed: " . implode(", ", $allowedExtensions);
                $messageType = "error";
            } else {
                // Everything is valid, insert into database

                $fileTitle = isset($_POST["title"]) && !empty($_POST["title"])
                    ? $_POST["title"]
                    : pathinfo($file["name"], PATHINFO_FILENAME);

                $result = uploadImage($file["name"], $fileTitle, $userId, $file["tmp_name"]);
                $message = $result["message"];
                $messageType = $result["messagetype"];
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Image</title>
    <link href="https://fonts.googleapis.com/css2?family=VT323&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/main.css">
    <link rel="stylesheet" href="/css/upload.css">
</head>

<body>
    <div class="container">
        <h1>Upload Image</h1>

        <?php if (!empty($message)): ?>
            <div class="message <?= $messageType ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="upload" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Image Title (optional)</label>
                <input type="text" id="title" name="title" placeholder="Enter a title for your image">
            </div>

            <div class="form-group">
                <label for="image">Select Image</label>
                <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.gif" required>
                <div class="file-specs">
                    Maximum size: 5MB<br>
                    Allowed types: JPG, JPEG, PNG, GIF
                </div>
            </div>

            <div class="form-actions">
                <a href="<?= $isLoggedIn ? 'dashboard' : 'home' ?>" class="back-button">Cancel</a>
                <button type="submit" class="upload-button">Upload Image</button>
            </div>
        </form>

        <div class="bottom-links">
            <a href="gallery">View Gallery</a> |
            <a href="<?= $isLoggedIn ? 'dashboard' : 'home' ?>">
                <?= $isLoggedIn ? 'Dashboard' : 'Home' ?>
            </a>
        </div>
    </div>
</body>

</html>