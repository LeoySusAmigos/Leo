<?php 
session_start(); 
include("../php/conexion.php"); 

// 1. SEGURIDAD: Solo admin
if (!isset($_SESSION['userID']) || $_SESSION['rol'] != 'admin') { 
    header("Location: ../index.php"); 
    exit(); 
} 

// 2. OBTENER ID DE LA PÁGINA
if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}
$pagina_id = $_GET['id']; 

// 3. CONSULTAR LA PÁGINA ACTUAL Y EL TÍTULO DEL LIBRO (Haciendo un INNER JOIN)
$sql = "SELECT p.*, l.titulo 
        FROM paginas_libro p 
        INNER JOIN libros l ON p.libro_id = l.libro_id 
        WHERE p.pagina_id = ?"; 

$stmt = $conn->prepare($sql); 
$stmt->bind_param("i", $pagina_id); 
$stmt->execute(); 
$resultado = $stmt->get_result(); 
$pagina = $resultado->fetch_assoc();

if (!$pagina) {
    echo "La página solicitada no existe.";
    exit();
}
?> 

<!DOCTYPE html> 
<html lang="es"> 
<head> 
    <meta charset="UTF-8"> 
    <title>Editar Página - Cuento</title> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"> 
</head> 
<body class="bg-light"> 
    
    <nav class="navbar navbar-dark bg-dark"> 
        <div class="container-fluid"> 
            <span class="navbar-brand">Panel de Control - Editar Contenido</span> 
            <a href="editar-libro.php?id=<?php echo $pagina['libro_id']; ?>" class="btn btn-secondary">Volver al Libro</a> 
        </div> 
    </nav> 

    <div class="container mt-5"> 
        <div class="card shadow p-4" style="max-width: 650px; margin: 0 auto;"> 
            
            <h2 class="mb-2 text-primary">Editar Página</h2> 
            <p class="text-muted mb-4">Cuento: <strong><?php echo htmlspecialchars($pagina['titulo']); ?></strong></p>
            
            <form method="POST" action="../php/editar-paginas.php"> 

                <input type="hidden" name="pagina_id" value="<?php echo $pagina['pagina_id']; ?>"> 
                <input type="hidden" name="libro_id" value="<?php echo $pagina['libro_id']; ?>"> 

                <div class="mb-3"> 
                    <label class="form-label fw-bold">Número de Página</label> 
                    <input type="number" 
                           name="numero_pagina" 
                           class="form-control" 
                           value="<?php echo $pagina['numero_pagina']; ?>" 
                           min="1" 
                           required> 
                </div> 

                <div class="mb-4"> 
                    <label class="form-label fw-bold">Texto de la Página</label> 
                    <textarea name="texto_pagina" 
                              class="form-control" 
                              rows="6" 
                              required><?php echo htmlspecialchars($pagina['texto_pagina']); ?></textarea> 
                    <small class="text-muted">Modifica las líneas que leerá el niño en esta sección del cuento.</small>
                </div> 

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary fw-bold py-2">Guardar Cambios en la Página</button> 
                    <a href="editar-libro.php?id=<?php echo $pagina['libro_id']; ?>" class="btn btn-outline-secondary">Cancelar</a>
                </div>
        
            </form> 
        </div>
    </div> 
</body> 
</html>