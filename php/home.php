<?php
session_start();
include "./database/utils.php";

$isLoggedIn = loginTokenIsValid(isset($_SESSION["login_token"]) ? $_SESSION["login_token"] : "");
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
        <!-- <div class="logo"><img src="https://upload.wikimedia.org/wikipedia/commons/2/2f/4chan_logo.svg" alt="Logo" /></div> -->

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

            <p class="description">Welcome back, Yoru!<br />How are you doing today?</p>

            <div class="button-group">
                <a class="button" href="upload">Upload Image</a>
                <a class="button" href="gallery">View Gallery</a>
                <a class="button" href="logout">Logout</a>
            </div>
        </div>
    </div>
</body>

</html>