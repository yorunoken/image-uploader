<?php
session_start();
include "./database/utils.php";

$isLoggedIn = loginTokenIsValid(isset($_SESSION["login_token"]) ? $_SESSION["login_token"] : "");

if (!$isLoggedIn) {
    // Redirect to login page if not logged in
    header("Location: login");
    exit();
}

$user = getUserFromLoginToken($_SESSION["login_token"]);

$userImages = getUserImages($user["id"]);
$totalImages = count($userImages);
$totalViews = array_sum(array_column($userImages, 'views'));
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Image Uploader</title>
    <link rel="stylesheet" href="/css/main.css">
    <link rel="stylesheet" href="/css/profile.css">
</head>

<body>
    <div class="container">
        <div class="terminal-header">
            <div class="terminal-buttons">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <div class="terminal-title">user@imageuploader:~/profile</div>
        </div>

        <div class="profile-header">
            <div class="profile-info">
                <p class="username"><?= htmlspecialchars($user["username"]) ?></p>
                <p class="user-status">// Online</p>
            </div>

            <div class="profile-stats">
                <div class="stat-box">
                    <span class="stat-value"><?= $totalImages ?></span>
                    <span class="stat-label">Uploads</span>
                </div>
                <div class="stat-box">
                    <span class="stat-value"><?= $totalViews ?></span>
                    <span class="stat-label">Total Views</span>
                </div>
            </div>
        </div>

        <div class="action-buttons">
            <a href="upload" class="action-button">
                <span class="action-icon">+</span>
                Upload New Image
            </a>
            <a href="home" class="action-button secondary">
                <span class="action-icon">←</span>
                Go Home
            </a>
        </div>

        <?php if (empty($userImages)): ?>
            <div class="no-images">
                <p>You haven't uploaded any images yet.</p>
                <p class="terminal-text">$ ls -la images/</p>
                <p class="terminal-response">total 0</p>
            </div>
        <?php else: ?>
            <h2>$ ls -la uploads/</h2>
            <div class="uploads-container">
                <?php foreach ($userImages as $image):
                    $imageId = $image["id"];
                    $imagePath = "images/$imageId." . $image["originalName"];
                    $imageUrl = "gallery?id=$imageId";
                    $uploadDate = date("M d, Y", strtotime($image["uploadDate"]));
                ?>
                    <div class="image-item">
                        <div class="image-preview">
                            <a href="<?= $imageUrl ?>">
                                <img src="<?= $imagePath ?>" alt="<?= htmlspecialchars($image["fileTitle"]) ?>">
                            </a>
                        </div>
                        <div class="image-details">
                            <h3><a href="<?= $imageUrl ?>"><?= htmlspecialchars($image["fileTitle"]) ?></a></h3>
                            <div class="image-meta">
                                <p class="upload-date"><span class="meta-label">Date:</span> <?= $uploadDate ?></p>
                                <p class="view-count"><span class="meta-label">Views:</span> <?= $image["views"] ?></p>
                                <p class="visibility-status"><span class="meta-label">Status:</span> <?= $image["isPublic"] ? 'Public' : 'Private' ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="bottom-links">
            <a href="gallery">View Gallery</a> |
            <a href="logout">Logout</a>
        </div>
    </div>
</body>

</html>