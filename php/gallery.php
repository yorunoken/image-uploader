<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image View</title>
    <link rel="stylesheet" href="/css/main.css">
    <link rel="stylesheet" href="/css/gallery.css">

</head>

<body>

    <?php
    include "./database/utils.php";

    if (isset($_GET["id"])) {
        $imageId = (int)$_GET["id"];
        $imageData = getImage($imageId);
        $imagePath = "images/$imageId." . $imageData["originalName"];

        if (file_exists("./public/$imagePath")) {
    ?>
            <a href="gallery" class="back-button">Back to Gallery</a>
            <div class="image-wrapper">
                <img src="<?= $imagePath; ?>" alt="<?= htmlspecialchars($imageData['originalName']); ?>" class="image-detail" />
            </div>


            <div class="details">
                <p><strong>Media Title:</strong> <?= htmlspecialchars($imageData["fileTitle"]); ?></p>
                <p><strong>File Name:</strong> <?= htmlspecialchars($imageData["originalName"]); ?></p>
                <p><strong>Uploaded by User ID:</strong> <?= htmlspecialchars($imageData["userId"]); ?></p>
                <p><strong>Upload Date:</strong> <?= date("M d, Y", strtotime($imageData["uploadDate"])) ?></p>
                <p><strong>Views:</strong> <?= htmlspecialchars($imageData["views"]); ?></p>
            </div>
            <?php
        } else {
            echo "<p>Image not found.</p>";
        }
    } else {
        $images = getImages();

        if (count($images) > 0) {
            echo '<a href="home" class="back-button">Back Home</a>';
            echo '<div class="gallery-grid">';
            foreach ($images as $imageData) {
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
    <?php
            }
            echo '</div>';
        } else {
            echo "<p>No images available in the gallery.</p>";
        }
    }
    ?>

</body>

</html>