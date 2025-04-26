<?php
include "./env.php";

$requestUri = $_SERVER['REQUEST_URI'];
$requestUri = strtok($requestUri, '?');
$requestUri = urldecode($requestUri);
$fileExtension = pathinfo($requestUri, PATHINFO_EXTENSION);

$staticExtensions = ['css', 'js', 'jpg', 'jpeg', 'png', 'gif', 'svg', 'ico'];
$mediaExtensions = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'ico'];

if (in_array($fileExtension, $staticExtensions)) {
    $file = $_SERVER["DOCUMENT_ROOT"] . "/public" . $requestUri;

    if (file_exists($file)) {
        if ($fileExtension === "css") {
            header("Content-Type: text/css");
        } else if ($fileExtension === "js") {
            header("Content-Type: application/javascript");
        } else if (in_array($fileExtension, $mediaExtensions)) {
            header("Content-Type: image/" . $fileExtension);
        }

        readfile($file);
    } else {
        header("HTTP/1.1 404 Not Found");
        echo $file;
        echo "File not found.";
    }
} else {
    if ($requestUri === '/') {
        include './php/home.php';
    } else {
        $file = $_SERVER['DOCUMENT_ROOT'] . '/php/' . ltrim($requestUri, '/');

        if (file_exists($file . '.php')) {
            include $file . '.php';
        } else {
            header("HTTP/1.1 404 Not Found");
            readfile($_SERVER['DOCUMENT_ROOT'] . "/public/html/404.html");
        }
    }
}
