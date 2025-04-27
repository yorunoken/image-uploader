<?php
session_start();
include "./database/utils.php";

$isLoggedIn = loginTokenIsValid(isset($_SESSION["login_token"]) ? $_SESSION["login_token"] : "");
$username = $isLoggedIn ? getUserFromLoginToken($_SESSION["login_token"])["username"] : "";

$recentImages = getImages(4);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Image Uploader</title>
    <link rel="stylesheet" href="/css/home.css">
    <link rel="stylesheet" href="/css/main.css">

    <style>
        .not-logged-in {
            display: <?= $isLoggedIn ? "none" : "block" ?>;
        }

        .logged-in {
            display: <?= $isLoggedIn ? "block" : "none" ?>;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="terminal-header">
            <div class="terminal-buttons">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <div class="terminal-title">user@imageuploader:~/home</div>
        </div>

        <div class="home-content">
            <div class="not-logged-in">
                <h1>Image Uploader</h1>
                <p class="description">Share your images with the world. Create an account to keep track of your uploads or upload anonymously.</p>

                <div class="button-group">
                    <a class="button" href="login">Login</a>
                    <a class="button" href="register">Register</a>
                    <a class="button" href="upload">Upload Image</a>
                    <a class="button" href="gallery">View Gallery</a>
                </div>
            </div>

            <div class="logged-in">
                <h1>Image Uploader</h1>

                <p class="description">Welcome back, <?= htmlspecialchars($username) ?>!<br />How are you doing today?</p>

                <div class="button-group">
                    <a class="button" href="profile">Profile</a>
                    <a class="button" href="upload">Upload Image</a>
                    <a class="button" href="gallery">View Gallery</a>
                    <a class="button" href="logout">Logout</a>
                </div>
            </div>

            <?php if (!empty($recentImages)): ?>
                <div class="recent-uploads">
                    <h2>Recently Uploaded</h2>
                    <div class="gallery-grid">
                        <?php foreach ($recentImages as $imageData):
                            $imageId = $imageData["id"];
                            $imagePath = "images/$imageId." . $imageData["originalName"];
                            $imageName = htmlspecialchars($imageData["fileTitle"]);
                            $imageUrl = "gallery?id=$imageId";
                        ?>
                            <div class="gallery-item">
                                <a href="<?= $imageUrl ?>">
                                    <img src="<?= $imagePath ?>" alt="<?= $imageName ?>" class="gallery-image" />
                                    <div class="overlay">
                                        <p class="image-title"><?= $imageName ?></p>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>