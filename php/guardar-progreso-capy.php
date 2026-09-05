<?php

session_start();

header('Content-Type: application/json');


// VERIFICAR SESIÓN

if (!isset($_SESSION['userID'])) {

    echo json_encode([
        'success' => false,
        'message' => 'Usuario no identificado.'
    ]);

    exit;
}


$userID = $_SESSION['userID'];


// CONEXIÓN

require_once 'conexion.php';


// RECIBIR DATOS

$datos = json_decode(
    file_get_contents("php://input"),
    true
);


if (!$datos) {

    echo json_encode([
        'success' => false,
        'message' => 'No se recibieron datos.'
    ]);

    exit;
}


// DATOS RECIBIDOS


$leccionID = isset($datos['leccion_id'])
    ? intval($datos['leccion_id'])
    : 0;

$actividadActual = isset($datos['actividad_actual'])
    ? intval($datos['actividad_actual'])
    : 1;

$porcentaje = isset($datos['porcentaje'])
    ? intval($datos['porcentaje'])
    : 0;

$puntos = isset($datos['puntos'])
    ? intval($datos['puntos'])
    : 0;

$completada = isset($datos['completada'])
    ? intval($datos['completada'])
    : 0;


// VALIDAR

if ($leccionID <= 0) {

    echo json_encode([
        'success' => false,
        'message' => 'Lección no válida.'
    ]);

    exit;
}


if ($porcentaje < 0) {
    $porcentaje = 0;
}

if ($porcentaje > 100) {
    $porcentaje = 100;
}

if ($actividadActual < 1) {
    $actividadActual = 1;
}

if ($puntos < 0) {
    $puntos = 0;
}


// GUARDAR PROGRESO

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

    VALUES
    (
        :userID,
        :leccion_id,
        :actividad_actual,
        :porcentaje,
        :puntos,
        :completada
    )

    ON DUPLICATE KEY UPDATE

        actividad_actual = VALUES(actividad_actual),
        porcentaje = VALUES(porcentaje),
        puntos = VALUES(puntos),
        completada = VALUES(completada),

        ultima_actualizacion = CURRENT_TIMESTAMP
";


$stmt = $conn->prepare($sql);


$stmt->execute([
    ':userID' => $userID,
    ':leccion_id' => $leccionID,
    ':actividad_actual' => $actividadActual,
    ':porcentaje' => $porcentaje,
    ':puntos' => $puntos,
    ':completada' => $completada
]);

// RESPUESTA

echo json_encode([
    'success' => true,
    'message' => 'Progreso guardado correctamente.'
]);