<?php
// ════════════════════════════════════════════════════════
//  php/actualizar-perfil.php — Leo & Friends
//  Recibe fetch() + FormData desde configuracion.js,
//  guarda archivos en images/perfiles/ y devuelve JSON.
// ════════════════════════════════════════════════════════
session_start();
include("conexion.php");

header('Content-Type: application/json');

// ── Protección de sesión ───────────────────────────────
if (!isset($_SESSION['userID'])) {
    echo json_encode(['ok' => false, 'msg' => 'Sesión expirada.']);
    exit();
}

$id          = $_SESSION['userID'];
$nombre_nino = trim($_POST['nombre_nino'] ?? '');
$nombre_papa = trim($_POST['nombre_papa'] ?? '');
$correo      = trim($_POST['correo']      ?? '');

// ── Validación ─────────────────────────────────────────
if (empty($nombre_nino) || empty($nombre_papa) || empty($correo)) {
    echo json_encode(['ok' => false, 'msg' => 'Todos los campos son obligatorios.']);
    exit();
}

// ── Subida de fotos ────────────────────────────────────
$carpeta        = "../images/perfiles/";
$tipos_ok       = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$max_bytes      = 2 * 1024 * 1024; // 2 MB máximo por foto

if (!is_dir($carpeta)) {
    mkdir($carpeta, 0755, true);
}

function subirFoto($campo, $carpeta, $tipos_ok, $max_bytes) {
    // Si no mandaron archivo o no hubo error, no hacer nada
    if (!isset($_FILES[$campo]) || $_FILES[$campo]['error'] === UPLOAD_ERR_NO_FILE) {
        return null; // Conservar foto actual en la BD
    }
    if ($_FILES[$campo]['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    // Validar tipo MIME real (no solo la extensión)
    $tipo = mime_content_type($_FILES[$campo]['tmp_name']);
    if (!in_array($tipo, $tipos_ok)) {
        return false;
    }

    // Validar tamaño
    if ($_FILES[$campo]['size'] > $max_bytes) {
        return false;
    }

    // Nombre único para evitar colisiones entre usuarios
    $ext            = explode('/', $tipo)[1]; // jpeg, png, etc.
    $ext            = ($ext === 'jpeg') ? 'jpg' : $ext;
    $nombre_archivo = uniqid('av_', true) . '.' . $ext;

    if (move_uploaded_file($_FILES[$campo]['tmp_name'], $carpeta . $nombre_archivo)) {
        return $nombre_archivo; // Devuelve solo el nombre, ej: "av_abc123.jpg"
    }
    return false;
}

$foto_nino_nueva  = subirFoto('foto_nino',  $carpeta, $tipos_ok, $max_bytes);
$foto_padre_nueva = subirFoto('foto_padre', $carpeta, $tipos_ok, $max_bytes);

// ── UPDATE dinámico ────────────────────────────────────
$campos = "nombre_nino = ?, nombre_papa = ?, correo = ?";
$params = [$nombre_nino, $nombre_papa, $correo];
$tipos  = "sss";

if ($foto_nino_nueva !== null && $foto_nino_nueva !== false) {
    $campos  .= ", foto_nino = ?";
    $params[] = $foto_nino_nueva;
    $tipos   .= "s";
}
if ($foto_padre_nueva !== null && $foto_padre_nueva !== false) {
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
    // Actualizar sesión para que el navbar refleje cambios sin re-loguear
    $_SESSION['nombre_nino'] = $nombre_nino;
    $_SESSION['nombre_papa'] = $nombre_papa;
    if ($foto_nino_nueva)  $_SESSION['foto_nino']  = $foto_nino_nueva;
    if ($foto_padre_nueva) $_SESSION['foto_padre'] = $foto_padre_nueva;

    // Devolver JSON con ok + nombres de archivo nuevos (para que JS actualice las imágenes)
    echo json_encode([
        'ok'         => true,
        'foto_nino'  => $foto_nino_nueva  ?: null,
        'foto_padre' => $foto_padre_nueva ?: null,
    ]);
} else {
    echo json_encode(['ok' => false, 'msg' => 'Error al guardar en la base de datos.']);
}
exit();
?>