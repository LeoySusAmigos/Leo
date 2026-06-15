

<?php 
session_start(); 
include("php/conexion.php"); 

// Proteger la página 
if (!isset($_SESSION['userID'])) { 
    header("Location: login.html"); 
    exit(); 
} 

// Obtener datos del usuario incluyendo ambos campos de fotos
$id = $_SESSION['userID']; 

$sql = "SELECT nombre_nino, nombre_papa, correo, foto_nino, foto_padre FROM usuarios WHERE userID = ?"; 
$stmt = $conn->prepare($sql); 
$stmt->bind_param("i", $id); 
$stmt->execute(); 
$resultado = $stmt->get_result(); 
$usuario = $resultado->fetch_assoc(); 

// Definir imágenes por defecto si las columnas en la BD están vacías
$avatar_nino = !empty($usuario['foto_nino']) ? "images/perfiles/" . $usuario['foto_nino'] : "images/default-nino.png";
$avatar_padre = !empty($usuario['foto_padre']) ? "images/perfiles/" . $usuario['foto_padre'] : "images/default-padre.png";
?> 

Solo es prueba, no es el código final.

<!DOCTYPE html> 
<html lang="es"> 
<head> 
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - Leo & Friends</title> 
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
            border: 4px solid #ffca28; /* Amarillo mágico para el niño */
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .avatar-frame.papa {
            border-color: #42a5f5; /* Azul para el papá */
        }
    </style>
</head> 
<body> 

<div class="container mt-5 mb-5"> 

    <div class="card shadow-sm p-4 profile-card bg-white mx-auto" style="max-width: 750px;"> 
        
        <div class="text-center mb-4">
            <div class="p-3 bg-warning bg-opacity-10 rounded-circle d-inline-block mb-2">
                <i class="fa-solid fa-user-gear fa-2x text-warning"></i>
            </div>
            <h3 class="fw-bold text-dark m-0">Configuración del Perfil</h3> 
            <p class="text-muted small">Personaliza tus datos y tus fotos de avatar de la plataforma</p>
        </div>

        <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
            <div class="alert alert-success border-0 shadow-sm rounded-3 d-flex align-items-center" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i> ¡Perfil y fotografías actualizados con éxito!
            </div>
        <?php endif; ?>

        <form method="POST" action="php/actualizar-perfil.php" enctype="multipart/form-data">
            
            <div class="row text-center mb-4">
                <div class="col-6">
                    <div class="p-3 bg-light rounded-3 h-100 border border-dashed">
                        <label class="form-label fw-bold text-secondary d-block mb-2">
                            <i class="fa-solid fa-child text-warning me-1"></i>Avatar del Pequeño
                        </label>
                        <img src="<?php echo $avatar_nino; ?>" alt="Avatar Niño" class="avatar-frame mb-3" id="viewNino">
                        <input type="file" name="foto_nino" class="form-control form-control-sm" accept="image/*" onchange="preview(this, 'viewNino')">
                    </div>
                </div>

                <div class="col-6">
                    <div class="p-3 bg-light rounded-3 h-100 border border-dashed">
                        <label class="form-label fw-bold text-secondary d-block mb-2">
                            <i class="fa-solid fa-user-tie text-primary me-1"></i>Avatar del Adulto
                        </label>
                        <img src="<?php echo $avatar_padre; ?>" alt="Avatar Padre" class="avatar-frame papa mb-3" id="viewPadre">
                        <input type="file" name="foto_padre" class="form-control form-control-sm" accept="image/*" onchange="preview(this, 'viewPadre')">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold text-muted">Nombre del Niño/a</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fa-solid fa-signature text-muted"></i></span>
                        <input type="text" name="nombre_nino" class="form-control" value="<?php echo htmlspecialchars($usuario['nombre_nino']); ?>" required>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold text-muted">Nombre de Papá/Mamá</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fa-solid fa-user-shield text-muted"></i></span>
                        <input type="text" name="nombre_papa" class="form-control" value="<?php echo htmlspecialchars($usuario['nombre_papa']); ?>" required>
                    </div>
                </div>

                <div class="col-md-8 mb-3">
                    <label class="form-label fw-semibold text-muted">Correo Electrónico</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fa-regular fa-envelope text-muted"></i></span>
                        <input type="email" name="correo" class="form-control" value="<?php echo htmlspecialchars($usuario['correo']); ?>" required>
                    </div>
                </div>

            </div>

            <div class="d-flex gap-2 justify-content-between pt-2 border-top">
                <a href="index.php" class="btn btn-light px-4 fw-semibold border">
                    <i class="fa-solid fa-house me-1"></i> Volver
                </a>
                <button type="submit" class="btn btn-warning px-4 fw-bold text-dark shadow-sm">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Actualizar Mi Perfil
                </button>
            </div>

        </form> 
    </div> 

</div>

<script>
function preview(input, elementId) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById(elementId).src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body> 
</html>