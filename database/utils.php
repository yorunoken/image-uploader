<?php
include "./database/db_connect.php";

function loginTokenIsValid(string $loginToken): bool
{
    global $conn;

    $stmt = $conn->prepare("SELECT 1 FROM users WHERE login_token = ?");
    if (!$stmt) {
        throw new Error("Problem preparing statement.");
    }

    $stmt->bind_param("s", $loginToken);
    $stmt->execute();
    $stmt->store_result();

    $loggedIn = $stmt->num_rows > 0;
    $stmt->close();

    return $loggedIn;
}

function getUserFromLoginToken(string $loginToken)
{
    global $conn;

    $stmt = $conn->prepare("SELECT id, username FROM users WHERE login_token = ?");
    if (!$stmt) {
        throw new Error("Problem preparing statement");
    }

    $stmt->bind_param("s", $loginToken);
    $stmt->execute();
    $stmt->store_result();

    $stmt->bind_result($userId, $username);
    $stmt->fetch();

    $user = array(
        "id" => (int)$userId,
        "username" => (string)$username
    );

    return $user;
}

function getImage(int $imageId)
{
    global $conn;

    $stmt = $conn->prepare("SELECT id, title, original_name, user_id, upload_date, views FROM images WHERE id = ? AND is_public = TRUE");
    if (!$stmt) {
        throw new Error("Problem preparing statement.");
    }

    $stmt->bind_param("i", $imageId);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        throw new Error("The image with that ID couldn't be found.");
    }

    $stmt->bind_result($id, $fileTitle, $originalName, $userId, $uploadDate, $views);
    $stmt->fetch();

    $imageData = array(
        "id" => (int)$id,
        "fileTitle" => (string)$fileTitle,
        "originalName" => (string)$originalName,
        "userId" => (int)$userId,
        "uploadDate" => (string)$uploadDate,
        "views" => (int)$views
    );
    return $imageData;
}

function uploadImage(string $originalName, string $fileTitle, $userId, $tempName)
{
    global $conn;

    $message = "";
    $messageType = "";

    $stmt = $conn->prepare("INSERT INTO images (title, original_name, user_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $fileTitle, $originalName, $userId);

    if ($stmt->execute()) {
        $imageId = $conn->insert_id;
        $targetPath = "./public/images/$imageId.$originalName";

        if (move_uploaded_file($tempName, $targetPath)) {
            $message = "Image uploaded successfully!";
            $messageType = "success";
        } else {
            $message = "Failed to save the uploaded file.";
            $messageType = "error";
        }
    } else {
        $message = "Database error. Please try again later.";
        $messageType = "error";
    }

    $stmt->close();

    return array(
        "message" => $message,
        "messagetype" => $messageType
    );
}

function getImages(string | null $limit = null)
{
    global $conn;

    $stmt = $conn->prepare("SELECT id, title, original_name, user_id, upload_date, views FROM images WHERE is_public = TRUE ORDER BY upload_date DESC" . ($limit ? " LIMIT ?" : ""));
    if (!$stmt) {
        throw new Error("Problem preparing statement.");
    }

    if ($limit) {
        $stmt->bind_param("s", $limit);
    }

    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        return [];
    }

    $stmt->bind_result($id, $fileTitle, $originalName, $userId, $uploadDate, $views);

    $images = array();

    while ($stmt->fetch()) {
        $images[] = array(
            "id" => (int)$id,
            "fileTitle" => (string)$fileTitle,
            "originalName" => (string)$originalName,
            "userId" => (int)$userId,
            "uploadDate" => (string)$uploadDate,
            "views" => (int)$views
        );
    }

    return $images;
}

function getUserImages($userId)
{
    global $conn;

    $stmt = $conn->prepare("SELECT id, title, original_name, upload_date, views, is_public FROM images WHERE user_id = ? ORDER BY upload_date DESC");
    if (!$stmt) {
        throw new Error("Problem preparing statement.");
    }

    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        return [];
    }

    $stmt->bind_result($id, $fileTitle, $originalName, $uploadDate, $views, $isPublic);

    $images = array();

    while ($stmt->fetch()) {
        $images[] = array(
            "id" => (int)$id,
            "fileTitle" => (string)$fileTitle,
            "originalName" => (string)$originalName,
            "uploadDate" => (string)$uploadDate,
            "views" => (int)$views,
            "isPublic" => (bool)$isPublic
        );
    }

    return $images;
}
