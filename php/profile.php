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
        <h1>Your Profile</h1>

        <div class="profile-header">
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

            <div class="profile-info">
                <p class="username"><?= htmlspecialchars($user["username"]) ?></p>
            </div>
        </div>

        <div class="action-buttons">
            <a href="upload" class="action-button">Upload New Image</a>
            <a href="home" class="action-button secondary">Go Home</a>
        </div>

        <?php if (empty($userImages)): ?>
            <div class="no-images">
                <p>You haven't uploaded any images yet.</p>
            </div>
        <?php else: ?>
            <h2>Your Uploads</h2>
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
                            <p class="upload-date">Uploaded: <?= $uploadDate ?></p>
                            <p class="view-count"><?= $image["views"] ?> views</p>
                            <p class="visibility-status"><?= $image["isPublic"] ? 'Public' : 'Private' ?></p>
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