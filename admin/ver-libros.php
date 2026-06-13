<?php 
session_start(); 
include("../php/conexion.php"); 

// 1. SEGURIDAD: Solo admin
if (!isset($_SESSION['userID']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

// 2. CONSULTA AVANZADA: Traemos los libros y el nombre de su nivel correspondiente
$sql = "SELECT l.*, n.niveles AS nombre_nivel 
        FROM libros l 
        INNER JOIN niveles n ON l.nivel_id = n.nivel_id
        ORDER BY l.libro_id DESC";
$resultado = $conn->query($sql); 
?> 

<!DOCTYPE html> 
<html lang="es"> 
<head> 
    <meta charset="UTF-8"> 
    <title>Administrar Cuentos - Leo & Friends</title> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"> 
</head> 
<body class="bg-light"> 

    <nav class="navbar navbar-dark bg-dark shadow-sm"> 
        <div class="container-fluid"> 
            <span class="navbar-brand fw-bold">
                <i class="fa-solid fa-book-open me-2 text-warning"></i> Admin Panel - Gestión de Libros
            </span> 
            <a href="dashboard.php" class="btn btn-outline-light btn-sm px-3">Volver al Inicio</a> 
        </div> 
    </nav>

    <div class="container mt-5"> 

        <?php if (isset($_GET['status']) && $_GET['status'] == 'book_deleted'): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i> ¡El libro y todas sus páginas asociadas han sido eliminados correctamente!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
 
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="m-0 fw-bold text-dark">Catálogo de Cuentos</h2> 
                <p class="text-muted m-0">Modifica, añade o elimina los libros disponibles para los niños.</p>
            </div>
            <a href="nuevo-libro.php" class="btn btn-success fw-bold shadow-sm px-4">
                <i class="fa-solid fa-plus me-2"></i> Agregar Nuevo Libro
            </a>
        </div>

        <div class="card shadow-sm border-0 overflow-hidden mb-5">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0"> 
                    <thead class="table-dark"> 
                        <tr> 
                            <th style="width: 8%;" class="text-center">ID</th> 
                            <th style="width: 12%;">Portada</th> 
                            <th style="width: 40%;">Título del Cuento</th> 
                            <th style="width: 15%;">Nivel</th>
                            <th style="width: 13%;">Tiempo</th>
                            <th style="width: 12%;" class="text-center">Acciones</th> 
                        </tr> 
                    </thead> 
                    <tbody> 
                        <?php if ($resultado->num_rows > 0): ?>
                            <?php while($libro = $resultado->fetch_assoc()): ?>    
                            <tr> 
                                <td class="text-center text-muted fw-bold">
                                    #<?php echo $libro['libro_id']; ?>
                                </td> 
                                
                                <td> 
                                    <img src="../images/cuentos/<?php echo $libro['portada']; ?>" 
                                         class="img-thumbnail shadow-sm" 
                                         style="max-width: 70px; height: auto; object-fit: cover;"> 
                                </td> 
                                
                                <td class="fw-bold text-dark fs-5">
                                    <?php echo htmlspecialchars($libro['titulo']); ?>
                                </td> 
                                
                                <td>
                                    <span class="badge bg-info text-dark px-3 py-2 fw-semibold fs-6 shadow-sm">
                                        <i class="fa-solid fa-layer-group me-1"></i> <?php echo $libro['nombre_nivel']; ?>
                                    </span>
                                </td> 

                                <td class="text-muted fw-medium">
                                    <i class="fa-regular fa-clock me-1 text-secondary"></i> <?php echo $libro['tiempo_estimado']; ?> min
                                </td> 
                                
                                <td class="text-center"> 
                                    <div class="btn-group shadow-sm" role="group">
                                        <a href="editar-libro.php?id=<?php echo $libro['libro_id']; ?>" 
                                           class="btn btn-warning btn-sm px-3" 
                                           title="Editar Libro y Páginas"> 
                                            <i class="fa-solid fa-pen-to-square"></i> 
                                        </a> 
                                        <a href="../php/borrar-libro.php?id=<?php echo $libro['libro_id']; ?>" 
                                           class="btn btn-danger btn-sm px-3" 
                                           title="Eliminar Libro"
                                           onclick="return confirm('¿Estás completamente seguro de eliminar este cuento? Esto borrará de forma permanente el libro y todas sus páginas.');"> 
                                            <i class="fa-solid fa-trash-can"></i> 
                                        </a> 
                                    </div>
                                </td> 
                            </tr> 
                            <?php endwhile; ?> 
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fa-regular fa-folder-open fa-3x mb-3 text-secondary"></i>
                                        <p class="fs-5 m-0">No hay libros registrados en el catálogo en este momento.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table> 
            </div>
        </div>
    </div> 

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script> 
</body>  
</html>