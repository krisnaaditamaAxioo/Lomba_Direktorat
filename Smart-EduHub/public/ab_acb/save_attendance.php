<?php
header('Content-Type: application/json');

// Matikan display error agar tidak merusak format JSON, tapi log tetap aktif
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once 'config.php';

function sendResponse($status, $message, $extra = []) {
    echo json_encode(array_merge(["status" => $status, "message" => $message], $extra));
    exit;
}

// Ambil input JSON dari aplikasi
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);

if (!$input) {
    sendResponse("error", "Data JSON tidak terbaca oleh server");
}

$nis = isset($input['nis']) ? $conn->real_escape_string(trim($input['nis'])) : '';
$timestamp = isset($input['timestamp']) ? $conn->real_escape_string($input['timestamp']) : '';
$location = isset($input['location']) ? $conn->real_escape_string($input['location']) : 'Mobile Device';

if (empty($nis) || empty($timestamp)) {
    sendResponse("error", "NIS atau Waktu tidak lengkap");
}

try {
    // 1. Cari student_id berdasarkan nis_siswa (Gambar 1)
    $resStudent = $conn->query("SELECT id FROM students WHERE nis_siswa = '$nis' LIMIT 1");
    if (!$resStudent || $resStudent->num_rows == 0) {
        sendResponse("error", "NIS '$nis' tidak ditemukan di database server");
    }
    $studentId = $resStudent->fetch_assoc()['id'];

    // 2. Ambil Settings (Gambar 2)
    $settings = [];
    $resSettings = $conn->query("SELECT `key`, `value` FROM settings");
    if ($resSettings) {
        while ($row = $resSettings->fetch_assoc()) {
            $settings[$row['key']] = $row['value'];
        }
    }
    $limitTime = isset($settings['check_in_time']) ? $settings['check_in_time'] : "08:00";
    $academicYear = isset($settings['academic_year']) ? $settings['academic_year'] : "2026/2027";

    // 3. Olah Waktu
    $timeUnix = strtotime($timestamp);
    $dateOnly = date("Y-m-d", $timeUnix);
    $timeOnly = date("H:i:s", $timeUnix);

    // 4. Tentukan Status (Gambar 3: Hadir / late)
    $status = (strtotime($timeOnly) <= strtotime($limitTime)) ? "Hadir" : "late";

    // 5. Cek Duplikasi (Satu kali sehari)
    $resCheck = $conn->query("SELECT id FROM attendances WHERE student_id = '$studentId' AND `date` = '$dateOnly' LIMIT 1");
    if ($resCheck && $resCheck->num_rows > 0) {
        sendResponse("error", "Siswa sudah absen hari ini ($dateOnly)");
    }

    // 6. Simpan (Sesuai kolom Gambar 3)
    $sqlInsert = "INSERT INTO attendances (student_id, `date`, check_in, `status`, academic_year, `location`, created_at, updated_at) 
                  VALUES ('$studentId', '$dateOnly', '$timeOnly', '$status', '$academicYear', '$location', NOW(), NOW())";

    if ($conn->query($sqlInsert)) {
        sendResponse("success", "Absensi Berhasil", ["attendance_status" => $status]);
    } else {
        throw new Exception("Gagal simpan ke database: " . $conn->error);
    }

} catch (Exception $e) {
    sendResponse("error", "Server Error: " . $e->getMessage());
}

$conn->close();
?>