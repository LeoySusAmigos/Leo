<?php 
session_start();
include("../php/conexion.php");

// SOLO ADMIN 
if (!isset($_SESSION['userID']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

// Consulta directa para traer los libros y poder elegir a cuál le meteremos páginas
$sqlLibros = "SELECT libro_id, titulo FROM libros";
$resultadoLibros = $conn->query($sqlLibros);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Página al Libro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"> 
</head>
<body>
    
    <nav class="navbar navbar-dark bg-dark"> 
        <div class="container-fluid"> 
            <span class="navbar-brand">Panel de Control - Páginas</span> 
            <a href="dashboard.php" class="btn btn-secondary">Volver</a> 
        </div> 
    </nav> 

    <div class="container mt-5"> 
    
        <div class="card shadow p-4" style="max-width: 600px; margin: 0 auto;"> 
    
            <h2 class="mb-4">Nueva Página de Cuento</h2> 
    
            <form method="POST" action="../php/crear-pagina.php"> 
    
                <div class="mb-3"> 
                    <label class="form-label">¿A qué libro pertenece esta página?</label> 
                    <select name="libro_id" class="form-control" required>
                        <option value="">-- Selecciona el cuento --</option>
                        
                        <?php while($libro = $resultadoLibros->fetch_assoc()): ?>
                            <option value="<?php echo $libro['libro_id']; ?>">
                                <?php echo $libro['titulo']; ?>
                            </option>
                        <?php endwhile; ?>
                        
                    </select>
                </div> 
    
                <div class="mb-3"> 
                    <label class="form-label">Número de Página</label> 
                    <input type="number" 
                        name="numero_pagina" 
                        class="form-control" 
                        placeholder="Ej: 1"
                        min="1"
                        required> 
                </div> 
    
                <div class="mb-3"> 
                    <label class="form-label">Texto de la Página</label> 
                    <textarea name="texto_pagina" 
                        class="form-control" 
                        rows="4" 
                        placeholder="Escribe aquí las líneas que leerá el niño en esta página..."
                        required></textarea> 
                </div> 
        
                <button type="submit" class="btn btn-primary w-100 mt-2"> 
                    Guardar Página
                </button> 
            
            </form>     
    
        </div> 
    
    </div>

</body>
</html>