<?php
session_start();

header('Content-Type: application/json');

include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $jsonRecibido = file_get_contents('php://input');
    $datos = json_decode($jsonRecibido, true);

    if (!$datos || !isset($datos['opciones']) || !is_array($datos['opciones'])) {
        echo json_encode(["success" => false, "message" => "Datos inválidos o incompletos."]);
        exit();
    }

    if (!isset($_SESSION['userID'])) {
        echo json_encode(["success" => false, "message" => "Sesión de usuario no encontrada."]);
        exit();
    }

    $usuario_id = $_SESSION['userID'];
    $opciones   = $datos['opciones'];

    // ── Guardar todas las respuestas en usuario_respuestas ──
    $todoCorrecto = true;
    $errorMensaje = "";

    $sql  = "INSERT INTO usuario_respuestas (usuario_id, opcion_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        foreach ($opciones as $opcion_id) {
            $opcion_id_clean = (int)$opcion_id;
            $stmt->bind_param("ii", $usuario_id, $opcion_id_clean);
            if (!$stmt->execute()) {
                $todoCorrecto = false;
                $errorMensaje = $stmt->error;
                break;
            }
        }
        $stmt->close();
    } else {
        $todoCorrecto = false;
        $errorMensaje = $conn->error;
    }

    // ── Detectar la edad según el opcion_id y guardarla en usuarios ──
    // opcion_id 10 = 6 años, 11 = 7 años, 12 = 8 años, 13 = 9 años o más
    $mapaEdades = [
        10 => 6,
        11 => 7,
        12 => 8,
        13 => 9,
    ];

    $edadDetectada = null;
    foreach ($opciones as $opcion_id) {
        $oid = (int)$opcion_id;
        if (isset($mapaEdades[$oid])) {
            $edadDetectada = $mapaEdades[$oid];
            break; // encontramos la edad, no seguimos buscando
        }
    }

    if ($edadDetectada !== null) {
        $sqlEdad  = "UPDATE usuarios SET edad_nino = ? WHERE userID = ?";
        $stmtEdad = $conn->prepare($sqlEdad);
        if ($stmtEdad) {
            $stmtEdad->bind_param("ii", $edadDetectada, $usuario_id);
            if (!$stmtEdad->execute()) {
                // No bloqueamos el flujo si esto falla, solo lo registramos
                error_log("Error al guardar edad_nino: " . $stmtEdad->error);
            }
            $stmtEdad->close();
        }
    }

    // ── Respuesta final ──
    if ($todoCorrecto) {
        echo json_encode(["success" => true, "message" => "Respuestas guardadas exitosamente."]);
    } else {
        echo json_encode(["success" => false, "message" => "Error en la base de datos: " . $errorMensaje]);
    }

    $conn->close();

} else {
    echo json_encode(["success" => false, "message" => "Método no permitido."]);
}
?>