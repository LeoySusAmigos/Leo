<?php
// ════════════════════════════════════════════════════════
//  php/cambiar-contrasena.php — Leo & Friends
//  Verifica la contraseña actual y guarda la nueva (hasheada)
// ════════════════════════════════════════════════════════
session_start();
include("conexion.php");

if (!isset($_SESSION['userID'])) {
    header('Location: ../login.html');
    exit();
}

$id        = $_SESSION['userID'];
$actual    = $_POST['actual']    ?? '';
$nueva     = $_POST['nueva']     ?? '';
$confirmar = $_POST['confirmar'] ?? '';

// ── Validaciones ────────────────────────────────────────
if (strlen($nueva) < 6) {
    header('Location: ../cambiar-contrasena.php?status=error&msg=muy_corta');
    exit();
}

if ($nueva !== $confirmar) {
    header('Location: ../cambiar-contrasena.php?status=error&msg=no_coinciden');
    exit();
}

// ── Verificar contraseña actual ────────────────────────
$sql  = "SELECT password FROM usuarios WHERE userID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario   = $resultado->fetch_assoc();

if (!$usuario || !password_verify($actual, $usuario['password'])) {
    header('Location: ../cambiar-contrasena.php?status=error&msg=actual_incorrecta');
    exit();
}

// ── Guardar la nueva contraseña (hasheada) ─────────────
$nuevaHasheada = password_hash($nueva, PASSWORD_DEFAULT);

$sqlUpdate = "UPDATE usuarios SET password = ? WHERE userID = ?";
$stmtUpdate = $conn->prepare($sqlUpdate);
$stmtUpdate->bind_param("si", $nuevaHasheada, $id);

if ($stmtUpdate->execute()) {
    header('Location: ../configuracion.php?status=success');
} else {
    header('Location: ../cambiar-contrasena.php?status=error&msg=db_error');
}
exit();
?>