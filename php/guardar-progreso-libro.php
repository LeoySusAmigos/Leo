<?php
session_start();

if (!isset($_SESSION['userID'])) {
    http_response_code(401);
    exit();
}

include 'conexion.php';

$data     = json_decode(file_get_contents('php://input'), true);
$libro_id = intval($data['libro_id'] ?? 0);
$segundos = intval($data['tiempo_segundos'] ?? 0);
$userID   = intval($_SESSION['userID']);

if ($libro_id <= 0) {
    http_response_code(400);
    exit();
}

$stmt = mysqli_prepare($conn, 
    "INSERT INTO progreso_libros (userID, libro_id, tiempo_segundos, fecha_leido)
     VALUES (?, ?, ?, NOW())
     ON DUPLICATE KEY UPDATE
        tiempo_segundos = VALUES(tiempo_segundos),
        fecha_leido     = VALUES(fecha_leido)"
);
mysqli_stmt_bind_param($stmt, "iii", $userID, $libro_id, $segundos);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['ok' => true]);
} else {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => mysqli_error($conn)]);
}