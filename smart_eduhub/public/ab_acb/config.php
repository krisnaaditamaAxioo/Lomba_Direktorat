<?php
$host = "localhost";
$user = "u597614007_smartacp";
$pass = "Sangatrahasia1*";
$db   = "u597614007_smartacp";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]));
}
?>