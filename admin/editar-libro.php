<?php 
session_start(); 
include("../php/conexion.php"); 

// 1. SEGURIDAD: Solo admin
if (!isset($_SESSION['userID']) || $_SESSION['rol'] != 'admin') { 
    header("Location: ../index.php"); 
    exit(); 
} 

// 2. OBTENER ID DEL LIBRO
if (!isset($_GET['id'])) {
    header("Location: ver-libros.php");
    exit();
}
$libro_id = $_GET['id']; 

// 3. CONSULTAR DATOS DEL LIBRO ACTUAL
$sqlLibro = "SELECT * FROM libros WHERE libro_id = ?"; 
$stmtLibro = $conn->prepare($sqlLibro); 
$stmtLibro->bind_param("i", $libro_id); 
$stmtLibro->execute(); 
$resultadoLibro = $stmtLibro->get_result(); 
$libro = $resultadoLibro->fetch_assoc();

if (!$libro) {
    echo "El libro solicitado no existe.";
    exit();
}

// 4. CONSULTAR LOS NIVELES DISPONIBLES (Para el select)
$sqlNiveles = "SELECT * FROM niveles";
$resultadoNiveles = $conn->query($sqlNiveles);

// 5. CONSULTAR LAS PÁGINAS DE ESTE LIBRO ESPECÍFICO
$sqlPaginas = "SELECT * FROM paginas_libro WHERE libro_id = ? ORDER BY numero_pagina ASC";
$stmtPaginas = $conn->prepare($sqlPaginas);
$stmtPaginas->bind_param("i", $libro_id);
$stmtPaginas->execute();
$resultadoPaginas = $stmtPaginas->get_result();
?> 

<!DOCTYPE html> 
<html lang="es"> 
<head> 
    <meta charset="UTF-8"> 
    <title>Panel de Edición - <?php echo htmlspecialchars($libro['titulo']); ?></title> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"> 
</head> 
<body class="bg-light"> 
    
    <nav class="navbar navbar-dark bg-dark"> 
        <div class="container-fluid"> 
            <span class="navbar-brand">Panel de Control - Administrar Cuento</span> 
            <a href="ver-libros.php" class="btn btn-secondary">Volver al Panel</a> 
        </div> 
    </nav> 

    <div class="container mt-5 mb-5"> 
        
        <div class="card shadow p-4 mb-5"> 
            <h2 class="mb-4 text-warning">Editar Datos del Libro</h2> 
            
            <form method="POST" action="../php/editar-libro.php" enctype="multipart/form-data"> 

                <input type="hidden" name="libro_id" value="<?php echo $libro['libro_id']; ?>"> 

                <div class="row">
                    <div class="col-md-6 mb-3"> 
                        <label class="form-label">Título del Cuento</label> 
                        <input type="text" name="titulo" class="form-control" value="<?php echo htmlspecialchars($libro['titulo']); ?>" required> 
                    </div> 

                    <div class="col-md-3 mb-3"> 
                        <label class="form-label">Tiempo Estimado (min)</label> 
                        <input type="number" name="tiempo_estimado" class="form-control" value="<?php echo $libro['tiempo_estimado']; ?>" required> 
                    </div> 

                    <div class="col-md-3 mb-3"> 
                        <label class="form-label">Nivel de Dificultad</label> 
                        <select name="nivel_id" class="form-control" required>
                            <?php while($nivel = $resultadoNiveles->fetch_assoc()): ?>
                                <option value="<?php echo $nivel['nivel_id']; ?>" <?php echo ($nivel['nivel_id'] == $libro['nivel_id']) ? 'selected' : ''; ?>>
                                    <?php echo $nivel['niveles']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div> 
                </div>

                <div class="row align-items-center mt-3">
                    <div class="col-md-3 mb-3 text-center"> 
                        <label class="form-label d-block">Portada Actual</label> 
                        <img src="../images/cuentos/<?php echo $libro['portada']; ?>" class="img-thumbnail shadow-sm" width="130"> 
                    </div> 

                    <div class="col-md-9 mb-3"> 
                        <label class="form-label">Cambiar Imagen de Portada (Opcional)</label> 
                        <input type="file" name="portada" class="form-control"> 
                        <small class="text-muted">Deja este espacio en blanco si no deseas cambiar la portada actual.</small>
                    </div> 
                </div>

                <hr>
                <button type="submit" class="btn btn-warning px-4 mt-2 fw-bold">Actualizar Datos Generales</button> 
            </form> 
        </div>

        <div class="card shadow p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="text-primary m-0">Páginas del Cuento</h2>
                <a href="paginas-libro.php?libro_id=<?php echo $libro_id; ?>" class="btn btn-success fw-bold">+ Agregar Nueva Página</a>
            </div>

            <?php if ($resultadoPaginas->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th style="width: 15%;">N° Página</th>
                                <th style="width: 65%;">Texto contenido</th>
                                <th style="width: 20%;" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($pagina = $resultadoPaginas->fetch_assoc()): ?>
                                <tr>
                                    <td class="fw-bold fs-5 text-center">
                                        <span class="badge bg-secondary px-3 py-2"><?php echo $pagina['numero_pagina']; ?></span>
                                    </td>
                                    <td>
                                        <p class="m-0 text-muted italic">"<?php echo htmlspecialchars($pagina['texto_pagina']); ?>"</p>
                                    </td>
                                    <td class="text-center">
                                        <a href="editar-pagina.php?id=<?php echo $pagina['pagina_id']; ?>" class="btn btn-sm btn-outline-primary me-1">Editar</a>
                                        <a href="../php/borrar-pagina.php?id=<?php echo $pagina['pagina_id']; ?>&libro_id=<?php echo $libro_id; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Seguro que deseas eliminar esta página?')">Eliminar</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info text-center py-4">
                    Este cuento todavía no tiene páginas registradas. ¡Haz clic en el botón de arriba para agregar la primera!
                </div>
            <?php endif; ?>
        </div>

    </div> 
</body> 
</html>