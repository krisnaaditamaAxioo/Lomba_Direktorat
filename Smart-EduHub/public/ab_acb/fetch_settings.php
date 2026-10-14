<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
include 'config.php';

$sql = "SELECT `key`, `value` FROM settings";
$result = $conn->query($sql);

$settings = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $settings[] = $row;
    }
    echo json_encode(["status" => "success", "data" => $settings]);
} else {
    echo json_encode(["status" => "error", "message" => "No settings found"]);
}

$conn->close();
?>