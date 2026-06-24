<?php
// ════════════════════════════════════════════════════════
//  php/actualizar-perfil.php — Leo & Friends
//  Guarda nombres y fotos en Base64 directo en la BD.
//  No necesita carpetas físicas en el servidor.
// ════════════════════════════════════════════════════════
session_start();
include("conexion.php");

if (!isset($_SESSION['userID'])) {
    header("Location: ../login.html");
    exit();
}

$id          = $_SESSION['userID'];
$nombre_nino = trim($_POST['nombre_nino'] ?? '');
$nombre_papa = trim($_POST['nombre_papa'] ?? '');
$correo      = trim($_POST['correo']      ?? '');

// ── Validación básica ──────────────────────────────────
if (empty($nombre_nino) || empty($nombre_papa) || empty($correo)) {
    header("Location: ../configuracion.php?status=error&msg=campos_vacios");
    exit();
}

// ── Convertir fotos a Base64 ───────────────────────────
// Si el usuario no sube foto nueva, la función devuelve null
// y conservamos la que ya estaba en la BD.
function fotoABase64($campo) {
    if (!isset($_FILES[$campo]) || $_FILES[$campo]['error'] === UPLOAD_ERR_NO_FILE) {
        return null; // No se subió nada, conservar foto actual
    }
    if ($_FILES[$campo]['error'] !== UPLOAD_ERR_OK) {
        return null; // Error al subir, ignorar
    }

    // Solo permitir imágenes
    $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $tipo = mime_content_type($_FILES[$campo]['tmp_name']);
    if (!in_array($tipo, $tiposPermitidos)) {
        return null;
    }

    // Leer el archivo y convertirlo a Base64
    $contenido = file_get_contents($_FILES[$campo]['tmp_name']);
    $base64    = base64_encode($contenido);

    // Guardamos con el prefijo data:image para usarlo directo en <img src="...">
    return 'data:' . $tipo . ';base64,' . $base64;
}

$foto_nino_nueva  = fotoABase64('foto_nino');
$foto_padre_nueva = fotoABase64('foto_padre');

// ── Construir UPDATE dinámicamente ────────────────────
// Solo actualizamos foto si se subió una nueva
$campos = "nombre_nino = ?, nombre_papa = ?, correo = ?";
$params = [$nombre_nino, $nombre_papa, $correo];
$tipos  = "sss";

if ($foto_nino_nueva !== null) {
    $campos  .= ", foto_nino = ?";
    $params[] = $foto_nino_nueva;
    $tipos   .= "s";
}

if ($foto_padre_nueva !== null) {
    $campos  .= ", foto_padre = ?";
    $params[] = $foto_padre_nueva;
    $tipos   .= "s";
}

$params[] = $id;
$tipos   .= "i";

$sql  = "UPDATE usuarios SET $campos WHERE userID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param($tipos, ...$params);

if ($stmt->execute()) {
    // Actualizar sesión para que el navbar refleje los cambios inmediatamente
    $_SESSION['nombre_nino'] = $nombre_nino;
    $_SESSION['nombre_papa'] = $nombre_papa;
    if ($foto_nino_nueva)  $_SESSION['foto_nino']  = $foto_nino_nueva;
    if ($foto_padre_nueva) $_SESSION['foto_padre'] = $foto_padre_nueva;

    header("Location: ../profile.php?status=success");
} else {
    header("Location: ../configuracion.php?status=error&msg=db_error");
}
exit();
?>