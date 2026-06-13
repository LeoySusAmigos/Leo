<?php 
session_start();
include("../php/conexion.php");

// 1. SEGURIDAD: Solo admin 
if (!isset($_SESSION['userID']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

// Recoger el ID del libro si es que viene desde la pantalla de edición
$libro_seleccionado = isset($_GET['libro_id']) ? $_GET['libro_id'] : '';

// 2. CONSULTAR TODOS LOS LIBROS PARA EL DESPLEGABLE
$sqlLibros = "SELECT libro_id, titulo FROM libros ORDER BY titulo ASC";
$resultadoLibros = $conn->query($sqlLibros);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Página al Libro - Leo & Friends</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"> 
</head>
<body class="bg-light">
    
    <nav class="navbar navbar-dark bg-dark shadow-sm"> 
        <div class="container-fluid"> 
            <span class="navbar-brand fw-bold">
                <i class="fa-solid fa-file-lines me-2 text-primary"></i>Panel de Control - Páginas
            </span> 
            <a href="<?php echo !empty($libro_seleccionado) ? 'editar-libro.php?id='.$libro_seleccionado : 'dashboard.php'; ?>" class="btn btn-outline-light btn-sm px-3">Volver</a> 
        </div> 
    </nav> 

    <div class="container mt-5 mb-5"> 
    
        <div class="card shadow border-0 p-4" style="max-width: 600px; margin: 0 auto; border-radius: 12px;"> 
    
            <div class="text-center mb-4">
                <div class="p-3 bg-primary bg-opacity-10 rounded-circle d-inline-block mb-2">
                    <i class="fa-solid fa-feather-pointed fa-2x text-primary"></i>
                </div>
                <h2 class="fw-bold text-dark m-0">Nueva Página de Cuento</h2> 
                <p class="text-muted small">Redacta el contenido que se mostrará en el visor de lectura.</p>
            </div>

            <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
                <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i> ¡Página guardada con éxito! Puedes seguir agregando más páginas.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
    
            <form method="POST" action="../php/crear-pagina.php"> 
    
                <div class="mb-3"> 
                    <label class="form-label fw-semibold text-secondary">¿A qué libro pertenece esta página?</label> 
                    <div class="input-group">
                        <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-book-bookmark"></i></span>
                        <select name="libro_id" class="form-select" required>
                            <option value="">Selecciona el cuento...</option>
                            
                            <?php while($libro = $resultadoLibros->fetch_assoc()): ?>
                                <option value="<?php echo $libro['libro_id']; ?>" <?php echo ($libro['libro_id'] == $libro_seleccionado) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($libro['titulo']); ?>
                                </option>
                            <?php endwhile; ?>
                            
                        </select>
                    </div>
                </div> 
    
                <div class="mb-3"> 
                    <label class="form-label fw-semibold text-secondary">Número de Página</label> 
                    <div class="input-group">
                        <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-list-ol"></i></span>
                        <input type="number" 
                            name="numero_pagina" 
                            class="form-control" 
                            placeholder="Ej: 1"
                            min="1"
                            required> 
                    </div>
                </div> 
    
                <div class="mb-4"> 
                    <label class="form-label fw-semibold text-secondary">Texto de la Página</label> 
                    <textarea name="texto_pagina" 
                        class="form-control" 
                        rows="5" 
                        placeholder="Escribe aquí las líneas rítmicas o narrativas que leerá el niño..."
                        required></textarea> 
                </div> 
        
                <button type="submit" class="btn btn-primary w-100 fw-bold py-2 shadow-sm"> 
                    <i class="fa-solid fa-floppy-disk me-2"></i> Guardar Página del Cuento
                </button> 
            
            </form>     
    
        </div> 
    
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script> 
</body>
</html>