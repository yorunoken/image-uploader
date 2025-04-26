<?php
include "./database/db_connect.php";

// class Image
// {
//     public int $id;
//     public string $fileName;
//     public string $originalFileName;
//     public int $userId;
//     public string $uploadDate;
//     public int $views;

//     public function __construct($id, $fileName, $userId, $uploadDate, $views)
//     {
//         $this->id = $id;
//         $this->fileName = $fileName;
//         $this->userId = $userId;
//         $this->uploadDate = $uploadDate;
//         $this->views = $views;
//     }
// }

function loginTokenIsValid(string $loginToken): bool
{
    global $conn;

    $stmt = $conn->prepare("SELECT 1 FROM users WHERE login_token = ?");
    if (!$stmt) {
        throw new Error("Problem prepareing statement.");
    }

    $stmt->bind_param("s", $loginToken);
    $stmt->execute();
    $stmt->store_result();

    $loggedIn = $stmt->num_rows > 0;
    $stmt->close();

    return $loggedIn;
}

function getImage(int $imageId)
{
    global $conn;

    $stmt = $conn->prepare("SELECT id, file_name, original_name, user_id, upload_date, views FROM images WHERE id = ? AND is_public = TRUE");
    if (!$stmt) {
        throw new Error("Problem prepareing statement.");
    }

    $stmt->bind_param("i", $imageId);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        throw new Error("The image with that ID couldn't be found.");
    }

    $stmt->bind_result($id, $fileName, $originalName, $userId, $uploadDate, $views);
    $stmt->fetch();

    $imageData = array(
        "id" => (int)$id,
        "fileName" => (string)$fileName,
        "originalName" => (string)$originalName,
        "userId" => (int)$userId,
        "uploadDate" => (string)$uploadDate,
        "views" => (int)$views
    );
    return $imageData;
}

function getImages()
{
    global $conn;

    $stmt = $conn->prepare("SELECT id, file_name, original_name, user_id, upload_date, views FROM images WHERE is_public = TRUE");
    if (!$stmt) {
        throw new Error("Problem preparing statement.");
    }

    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        throw new Error("No public images found.");
    }

    $stmt->bind_result($id, $fileName, $originalName, $userId, $uploadDate, $views);

    $images = array();

    while ($stmt->fetch()) {
        $images[] = array(
            "id" => (int)$id,
            "fileName" => (string)$fileName,
            "originalName" => (string)$originalName,
            "userId" => (int)$userId,
            "uploadDate" => (string)$uploadDate,
            "views" => (int)$views
        );
    }

    return $images;
}
