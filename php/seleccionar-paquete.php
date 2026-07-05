<?php
// ════════════════════════════════════════════════════════
//  php/seleccionar-paquete.php — Leo & Friends
//  Recibe el paqueteID elegido, actualiza o crea la
//  suscripción del usuario y devuelve JSON.
// ════════════════════════════════════════════════════════
session_start();
include("conexion.php");

header('Content-Type: application/json');

if (!isset($_SESSION['userID'])) {
    echo json_encode(['ok' => false, 'msg' => 'Sesión expirada.']);
    exit();
}

$datos     = json_decode(file_get_contents('php://input'), true);
$paqueteID = (int)($datos['paqueteID'] ?? 0);
$userID    = (int)$_SESSION['userID'];

if ($paqueteID <= 0) {
    echo json_encode(['ok' => false, 'msg' => 'Paquete inválido.']);
    exit();
}

// Verificar que el paquete existe y está activo
$stmtVerificar = $conn->prepare("SELECT nombre FROM paquetes WHERE paqueteID = ? AND activo = 1");
$stmtVerificar->bind_param("i", $paqueteID);
$stmtVerificar->execute();
$resPaquete = $stmtVerificar->get_result()->fetch_assoc();

if (!$resPaquete) {
    echo json_encode(['ok' => false, 'msg' => 'El paquete seleccionado no existe.']);
    exit();
}

// ── Verificar si ya tiene suscripción activa ───────────
$stmtCheck = $conn->prepare("SELECT suscripcionID FROM suscripciones WHERE userID = ? AND estado = 'activa'");
$stmtCheck->bind_param("i", $userID);
$stmtCheck->execute();
$subExistente = $stmtCheck->get_result()->fetch_assoc();

if ($subExistente) {
    // Ya tiene suscripción activa → actualizar el paqueteID
    $stmtUpdate = $conn->prepare("UPDATE suscripciones SET paqueteID = ?, fecha_inicio = NOW() WHERE suscripcionID = ?");
    $stmtUpdate->bind_param("ii", $paqueteID, $subExistente['suscripcionID']);
    $ok = $stmtUpdate->execute();
} else {
    // No tiene suscripción → crear una nueva
    $stmtInsert = $conn->prepare("INSERT INTO suscripciones (userID, paqueteID, estado) VALUES (?, ?, 'activa')");
    $stmtInsert->bind_param("ii", $userID, $paqueteID);
    $ok = $stmtInsert->execute();
}

if ($ok) {
    // Guardar en sesión para que configuracion.php lo refleje sin recargar
    $_SESSION['paqueteID']    = $paqueteID;
    $_SESSION['paquete_nombre'] = $resPaquete['nombre'];

    echo json_encode([
        'ok'     => true,
        'nombre' => $resPaquete['nombre']
    ]);
} else {
    echo json_encode(['ok' => false, 'msg' => 'Error al guardar en la base de datos.']);
}
exit();
?>