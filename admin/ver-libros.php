<?php 
session_start(); 
include("../php/conexion.php"); 

// SOLO ADMIN 
if (!isset($_SESSION['userID']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

// Consulta directa a la tabla libros (Sencilla, como a ti te gusta)
$sql = "SELECT * FROM libros";
$resultado = $conn->query($sql); 
?> 

<!DOCTYPE html> 
<html lang="es"> 
<head> 
    <meta charset="UTF-8"> 
    <title>Ver Libros</title> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"> 
</head> 
<body> 
    <nav class="navbar navbar-dark bg-dark"> 
        <div class="container-fluid"> 
            <span class="navbar-brand">Admin - Libros</span> 
            <a href="dashboard.php" class="btn btn-secondary">Volver</a> 
        </div> 
    </nav>

    <div class="container mt-5"> 
 
        <h2 class="mb-4">Lista de Libros</h2> 
 
        <table class="table table-bordered table-hover align-middle"> 
 
            <thead class="table-dark"> 
                <tr> 
                    <th>#</th> 
                    <th>Portada</th> 
                    <th>Título</th> 
                    <th>Nivel</th>
                    <th>Tiempo Estimado</th>
                    <th>Acciones</th> 
                </tr> 
            </thead> 
 
            <tbody> 
 
                <?php while($libro = $resultado->fetch_assoc()): ?>    
    
                <tr> 
                    <th scope="row"><?php echo $libro['libro_id']; ?></th> 
    
                    <td> 
                        <img src="../img/libros/<?php echo $libro['portada']; ?>" width="60"> 
                    </td> 
    
                    <td><?php echo $libro['titulo']; ?></td> 
    
                    <td>Nivel <?php echo $libro['nivel']; ?></td> 

                    <td><?php echo $libro['tiempo_estimado_min']; ?> min</td> 
    
                    <td> 
                        <a href="editar-libro.php?id=<?php echo $libro['libro_id']; ?>" class="btn btn-warning btn-sm"> 
                            <i class="fa-solid fa-pen"></i> 
                        </a> 
    
                        <a href="eliminar-libro.php?id=<?php echo $libro['libro_id']; ?>" class="btn btn-danger btn-sm" 
                        onclick="return confirm('¿Seguro que deseas eliminar este libro?');"> 
                            <i class="fa-solid fa-trash"></i> 
                        </a> 
                    </td> 
                </tr> 

                <?php endwhile; ?> 

            </tbody>

        </table> 
    
    </div> 

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script> 
</body>  
</html>