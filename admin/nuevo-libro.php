<?php 
session_start();
include("../php/conexion.php");

// 1. SEGURIDAD: Solo admin 
if (!isset($_SESSION['userID']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

// 2. CONSULTAR NIVELES DISPONIBLES DE LA BASE DE DATOS
$sqlNiveles = "SELECT * FROM niveles ORDER BY nivel_id ASC";
$resultadoNiveles = $conn->query($sqlNiveles);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Nuevo Libro - Leo & Friends</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"> 
</head>
<body class="bg-light">
    
    <nav class="navbar navbar-dark bg-dark shadow-sm"> 
        <div class="container-fluid"> 
            <span class="navbar-brand fw-bold">
                <i class="fa-solid fa-book me-2 text-success"></i>Panel de Control - Cuentos
            </span> 
            <a href="dashboard.php" class="btn btn-outline-light btn-sm px-3">Volver al Inicio</a> 
        </div> 
    </nav> 

    <div class="container mt-5 mb-5"> 
    
        <div class="card shadow border-0 p-4" style="max-width: 600px; margin: 0 auto; border-radius: 12px;"> 
    
            <div class="text-center mb-4">
                <div class="p-3 bg-success bg-opacity-10 rounded-circle d-inline-block mb-2">
                    <i class="fa-solid fa-folder-plus fa-2x text-success"></i>
                </div>
                <h2 class="fw-bold text-dark m-0">Nuevo Libro Infantil</h2> 
                <p class="text-muted small">Registra un cuento base para luego añadirle sus páginas.</p>
            </div>

            <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
                <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i> ¡Cuento registrado con éxito en el catálogo!
                    <button type="button" class="btn-close" data-bs-alert="dismiss" aria-label="Close"></button>
                </div>
            <?php endif; ?>
    
            <form method="POST" action="../php/crear-libro.php" enctype="multipart/form-data"> 
    
                <div class="mb-3"> 
                    <label class="form-label fw-semibold text-secondary">Título del Cuento</label> 
                    <div class="input-group">
                        <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-font"></i></span>
                        <input type="text" 
                            name="titulo" 
                            class="form-control" 
                            placeholder="Ej: Leo y sus Amigos"
                            required> 
                    </div>
                </div> 
    
                <div class="mb-3"> 
                    <label class="form-label fw-semibold text-secondary">Tiempo Estimado de Lectura (minutos)</label> 
                    <div class="input-group">
                        <span class="input-group-text bg-white text-muted"><i class="fa-regular fa-clock"></i></span>
                        <input type="number" 
                            name="tiempo_estimado" 
                            class="form-control" 
                            placeholder="Ej: 5"
                            min="1"
                            required> 
                    </div>
                </div> 
    
                <div class="mb-3"> 
                    <label class="form-label fw-semibold text-secondary">Nivel de Dificultad</label> 
                    <div class="input-group">
                        <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-layer-group"></i></span>
                        <select name="nivel" class="form-select" required>
                            <option value="">Selecciona un nivel...</option>
                            <?php while($nivel = $resultadoNiveles->fetch_assoc()): ?>
                                <option value="<?php echo $nivel['nivel_id']; ?>">
                                    <?php echo $nivel['niveles']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div> 
                
                <div class="mb-4"> 
                    <label class="form-label fw-semibold text-secondary">Imagen de Portada</label> 
                    <div class="input-group">
                        <span class="input-group-text bg-white text-muted"><i class="fa-regular fa-image"></i></span>
                        <input type="file" 
                            name="portada" 
                            class="form-control" 
                            accept="image/*"
                            required> 
                    </div>
                    <small class="text-muted d-block mt-1">El archivo se guardará automáticamente en la carpeta de cuentos.</small>
                </div> 
        
                <button type="submit" class="btn btn-success w-100 fw-bold py-2 shadow-sm"> 
                    <i class="fa-solid fa-cloud-arrow-up me-2"></i> Guardar Cuento en Catálogo
                </button>
            
            </form>     
    
        </div> 
    
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script> 
</body>
</html>