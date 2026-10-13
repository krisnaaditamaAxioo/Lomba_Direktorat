<?php
header('Content-Type: application/json');
require_once 'config.php';

// Mengambil nis_siswa dan aliaskan sebagai 'nis' agar sesuai model Student di Android
$sql = "SELECT nis_siswa as nis, nama_lengkap FROM students WHERE nis_siswa IS NOT NULL AND nis_siswa != ''";
$result = $conn->query($sql);

if ($result) {
    $students = [];
    while($row = $result->fetch_assoc()) {
        $students[] = [
            "nis" => trim($row['nis']),
            "nama_lengkap" => $row['nama_lengkap']
        ];
    }
    echo json_encode(["status" => "success", "data" => $students]);
} else {
    echo json_encode(["status" => "error", "message" => $conn->error]);
}

$conn->close();
?>
