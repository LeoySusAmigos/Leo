<?php 
session_start();
include("../php/conexion.php");

// SOLO ADMIN 
if (!isset($_SESSION['userID']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Libro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"> 
</head>
<body>
    
    <nav class="navbar navbar-dark bg-dark"> 
        <div class="container-fluid"> 
            <span class="navbar-brand">Panel de Control - Cuentos</span> 
            <a href="dashboard.php" class="btn btn-secondary">Volver</a> 
        </div> 
    </nav> 

    <div class="container mt-5"> 
    
        <div class="card shadow p-4" style="max-width: 600px; margin: 0 auto;"> 
    
            <h2 class="mb-4">Nuevo Libro Infantil</h2> 
    
            <form method="POST" action="../php/crear-libro.php" enctype="multipart/form-data"> 
    
                <div class="mb-3"> 
                    <label class="form-label">Título del Cuento</label> 
                    <input type="text" 
                        name="titulo" 
                        class="form-control" 
                        placeholder="Ej: La araña y la manzana"
                        required> 
                </div> 
    
                <div class="mb-3"> 
                    <label class="form-label">Tiempo Estimado de Lectura (en minutos)</label> 
                    <input type="number" 
                        name="tiempo_estimado_min" 
                        class="form-control" 
                        placeholder="Ej: 2"
                        required> 
                </div> 
    
                <div class="mb-3"> 
                    <label class="form-label">Nivel de Dificultad</label> 
                    <select name="nivel" class="form-control" required>
                        <option value="">-- Selecciona un nivel --</option>
                        <option value="1">Nivel 1 (4 líneas • palabras simples)</option>
                        <option value="2">Nivel 2 (6 líneas • palabras completas)</option>
                        <option value="3">Nivel 3</option>
                        <option value="4">Nivel 4</option>
                        <option value="5">Nivel 5</option>
                    </select>
                </div> 
                
                <div class="mb-3"> 
                    <label class="form-label">Imagen de Portada</label> 
                    <input type="file" 
                        name="imagen_url" 
                        class="form-control" 
                        required> 
                </div> 
        
                <button type="submit" class="btn btn-success w-100 mt-2"> 
                    Guardar Cuento en Catálogo
                </button>
            
            </form>     
    
        </div> 
    
    </div>

</body>
</html>