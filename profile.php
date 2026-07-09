<?php
session_start();
include("php/conexion.php");

if (!isset($_SESSION['userID'])) {
    header("Location: login.html");
    exit();
}

// Siempre leemos de la BD para mostrar lo más reciente
$id  = $_SESSION['userID'];
$sql = "SELECT nombre_nino, nombre_papa, correo, foto_nino, foto_padre FROM usuarios WHERE userID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();

// Si la columna está vacía/NULL usamos un avatar genérico
$avatar_nino  = !empty($usuario['foto_nino'])  ? $usuario['foto_nino']  : "images/default-nino.png";
$avatar_padre = !empty($usuario['foto_padre']) ? $usuario['foto_padre'] : "images/default-padre.png";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            background-color: #f4f7f6;
            font-family: 'Quicksand', 'Nunito', sans-serif;
        }
        .profile-card {
            border: none;
            border-radius: 16px;
        }
        .avatar-frame {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid #ffca28;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .avatar-frame.papa {
            border-color: #42a5f5;
        }
        .avatar-label {
            font-size: .8rem;
            color: #888;
            margin-top: 8px;
        }
        .dato-fila {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .dato-fila:last-child { border-bottom: none; }
        .dato-fila .icono {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #f4f7f6;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .dato-fila .etiqueta {
            font-size: .78rem;
            color: #aaa;
            font-weight: 600;
            margin-bottom: 2px;
        }
        .dato-fila .valor {
            font-size: .97rem;
            font-weight: 700;
            color: #222;
        }
    </style>
</head>
<body>

<div class="container mt-5 mb-5">
    <div class="card shadow-sm p-4 profile-card bg-white mx-auto" style="max-width: 750px;">

        <!-- ENCABEZADO -->
        <div class="text-center mb-4">
            <div class="p-3 bg-warning bg-opacity-10 rounded-circle d-inline-block mb-2">
                <i class="fa-solid fa-user fa-2x text-warning"></i>
            </div>
            <h3 class="fw-bold text-dark m-0">Perfil</h3>
            <p class="text-muted small">Información de tu cuenta en Leo &amp; Friends</p>
        </div>

        <?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
            <div class="alert alert-success border-0 shadow-sm rounded-3 d-flex align-items-center">
                <i class="fa-solid fa-circle-check me-2"></i> ¡Perfil actualizado con éxito!
            </div>
        <?php endif; ?>

        <!-- AVATARES (solo visualización) -->
        <div class="row text-center mb-4">
            <div class="col-6">
                <div class="p-3 bg-light rounded-3 h-100">
                    <p class="fw-bold text-secondary small mb-2">
                        <i class="fa-solid fa-child text-warning me-1"></i>Avatar del Pequeño
                    </p>
                    <img src="<?= htmlspecialchars($avatar_nino) ?>"
                         alt="Avatar Niño" class="avatar-frame">
                    <p class="avatar-label"><?= htmlspecialchars($usuario['nombre_nino']) ?></p>
                </div>
            </div>
            <div class="col-6">
                <div class="p-3 bg-light rounded-3 h-100">
                    <p class="fw-bold text-secondary small mb-2">
                        <i class="fa-solid fa-user-tie text-primary me-1"></i>Avatar del Adulto
                    </p>
                    <img src="<?= htmlspecialchars($avatar_padre) ?>"
                         alt="Avatar Padre" class="avatar-frame papa">
                    <p class="avatar-label"><?= htmlspecialchars($usuario['nombre_papa']) ?></p>
                </div>
            </div>
        </div>

        <!-- DATOS solo lectura -->
        <div class="px-2">
            <div class="dato-fila">
                <div class="icono"><i class="fa-solid fa-child text-warning"></i></div>
                <div>
                    <div class="etiqueta">Nombre del niño/a</div>
                    <div class="valor"><?= htmlspecialchars($usuario['nombre_nino']) ?></div>
                </div>
            </div>
            <div class="dato-fila">
                <div class="icono"><i class="fa-solid fa-user-shield text-primary"></i></div>
                <div>
                    <div class="etiqueta">Nombre del papá/mamá</div>
                    <div class="valor"><?= htmlspecialchars($usuario['nombre_papa']) ?></div>
                </div>
            </div>
            <div class="dato-fila">
                <div class="icono"><i class="fa-regular fa-envelope text-secondary"></i></div>
                <div>
                    <div class="etiqueta">Correo electrónico</div>
                    <div class="valor"><?= htmlspecialchars($usuario['correo']) ?></div>
                </div>
            </div>
        </div>

        <!-- BOTONES -->
        <div class="d-flex gap-2 justify-content-between pt-3 mt-3 border-top">
            <a href="index.php" class="btn btn-light px-4 fw-semibold border">
                <i class="fa-solid fa-house me-1"></i> Volver
            </a>
            <a href="configuracion.php" class="btn btn-warning px-4 fw-bold text-dark shadow-sm">
                <i class="fa-solid fa-pen me-1"></i> Editar en Configuración
            </a>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>