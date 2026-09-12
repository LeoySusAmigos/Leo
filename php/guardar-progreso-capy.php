<?php

session_start();

header('Content-Type: application/json; charset=utf-8');

function responder($success, $message = '', $datos = [])
{
    echo json_encode(
        array_merge(
            [
                'success' => $success,
                'message' => $message
            ],
            $datos
        ),
        JSON_UNESCAPED_UNICODE
    );

    exit;
}

if (!isset($_SESSION['userID'])) {
    responder(false, 'Usuario no identificado.');
}

$userID = intval($_SESSION['userID']);

require_once 'conexion.php';

$contenido = file_get_contents('php://input');
$datos = json_decode($contenido, true);

if (!is_array($datos)) {
    $datos = $_POST;
}

if (!is_array($datos) || empty($datos)) {
    responder(false, 'No se recibieron datos válidos.');
}

$leccionID = isset($datos['leccion_id']) ? intval($datos['leccion_id']) : 0;
$actividadActual = isset($datos['actividad_actual']) ? intval($datos['actividad_actual']) : 1;
$porcentaje = isset($datos['porcentaje']) ? intval($datos['porcentaje']) : 0;
$puntos = isset($datos['puntos']) ? intval($datos['puntos']) : 0;
$completada = isset($datos['completada']) ? intval($datos['completada']) : 0;

if ($leccionID <= 0) {
    responder(false, 'La lección no es válida.');
}

$actividadActual = max(1, $actividadActual);
$porcentaje = max(0, min(100, $porcentaje));
$puntos = max(0, $puntos);
$completada = $completada === 1 ? 1 : 0;

$sqlVerificar = "
    SELECT id
    FROM capy_lecciones
    WHERE id = ?
      AND activa = 1
    LIMIT 1
";

$stmtVerificar = $conn->prepare($sqlVerificar);

if (!$stmtVerificar) {
    responder(false, 'No se pudo verificar la lección.');
}

$stmtVerificar->bind_param('i', $leccionID);
$stmtVerificar->execute();
$resultadoVerificar = $stmtVerificar->get_result();

if ($resultadoVerificar->num_rows === 0) {
    $stmtVerificar->close();
    responder(false, 'La lección no existe o está desactivada.');
}

$stmtVerificar->close();

$sqlActividades = "
    SELECT COUNT(*) AS total
    FROM capy_actividades
    WHERE leccion_id = ?
      AND activa = 1
";

$stmtActividades = $conn->prepare($sqlActividades);

if (!$stmtActividades) {
    responder(false, 'No se pudo comprobar la lección.');
}

$stmtActividades->bind_param('i', $leccionID);
$stmtActividades->execute();
$resultadoActividades = $stmtActividades->get_result();
$filaActividades = $resultadoActividades->fetch_assoc();
$totalActividades = intval($filaActividades['total'] ?? 0);
$stmtActividades->close();

if ($totalActividades <= 0) {
    responder(false, 'La lección no tiene actividades activas.');
}

$actividadActual = min($actividadActual, $totalActividades);

if ($completada === 1) {
    $actividadActual = $totalActividades;
    $porcentaje = 100;
}

$sql = "
    INSERT INTO capy_progreso
    (
        userID,
        leccion_id,
        actividad_actual,
        porcentaje,
        puntos,
        completada
    )
    VALUES (?, ?, ?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE
        actividad_actual = GREATEST(actividad_actual, VALUES(actividad_actual)),
        porcentaje = GREATEST(porcentaje, VALUES(porcentaje)),
        puntos = GREATEST(puntos, VALUES(puntos)),
        completada = GREATEST(completada, VALUES(completada)),
        ultima_actualizacion = CURRENT_TIMESTAMP
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    responder(false, 'No se pudo guardar el progreso.');
}

$stmt->bind_param(
    'iiiiii',
    $userID,
    $leccionID,
    $actividadActual,
    $porcentaje,
    $puntos,
    $completada
);

if (!$stmt->execute()) {
    $stmt->close();
    responder(false, 'Ocurrió un error al guardar el progreso.');
}

$stmt->close();

responder(
    true,
    'Progreso guardado correctamente.',
    [
        'actividad_actual' => $actividadActual,
        'porcentaje' => $porcentaje,
        'puntos' => $puntos,
        'completada' => $completada
    ]
);