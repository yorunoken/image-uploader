<?php
$serverLocation = $_ENV["MYSQL_URL"];
$username = $_ENV["MYSQL_USERNAME"];
$password = $_ENV["MYSQL_PASSWORD"];
$databaseName = $_ENV["MYSQL_DATABASE"];

$conn = new mysqli($serverLocation, $username, $password, $databaseName);

if ($conn->connect_error) {
    die("Connection failed " . $conn->connect_error);
}
