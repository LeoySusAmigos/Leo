<?php
session_start();

if (!isset($_SESSION['userID'])) {
    http_response_code(401);
    exit();
}

include 'php/conexion.php';

$data     = json_decode(file_get_contents('php://input'), true);
$libro_id = intval($data['libro_id'] ?? 0);
$segundos = intval($data['tiempo_segundos'] ?? 0);
$userID   = intval($_SESSION['userID']);

if ($libro_id <= 0) {
    http_response_code(400);
    exit();
}

// Insertar o actualizar progreso
$sql = "INSERT INTO progreso_libros (userID, libro_id, tiempo_segundos, fecha_lectura)
        VALUES ($userID, $libro_id, $segundos, NOW())
        ON DUPLICATE KEY UPDATE
            tiempo_segundos = $segundos,
            fecha_lectura   = NOW()";

mysqli_query($conn, $sql);

echo json_encode(['ok' => true]);