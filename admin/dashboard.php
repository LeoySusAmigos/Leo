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
    <title>Panel de Administración - Leo & Friends</title> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"> 
</head>
<body class="bg-light"> 

    <nav class="navbar navbar-expand-lg bg-dark navbar-dark shadow-sm"> 
        <div class="container-fluid"> 
            <a class="navbar-brand fw-bold" href="#">
                <i class="fa-solid fa-wand-magic-sparkles text-warning me-2"></i>Leo & Friends Admin
            </a> 
            <div class="ms-auto"> 
                <a class="btn btn-danger btn-sm px-3 fw-semibold" href="../php/logout.php"> 
                    <i class="fa-solid fa-right-from-bracket me-1"></i> Cerrar sesión 
                </a> 
            </div> 
        </div> 
    </nav> 

    <div class="container mt-5"> 
        
        <div class="p-4 mb-5 bg-white rounded shadow-sm d-flex align-items-center justify-content-between">
            <div>
                <h2 class="fw-bold text-dark m-0">¡Hola, Administrador!</h2> 
                <p class="text-muted m-0 mt-1">Bienvenido al panel de control de Leo & Friends. ¿Qué deseas gestionar hoy?</p>
            </div>
            <span class="badge bg-secondary p-2 px-3 fw-medium">
                <i class="fa-regular fa-calendar me-1"></i> Mode: Activo
            </span>
        </div>

        <div class="row"> 

            <div class="col-md-4 mb-4"> 
                <div class="card h-100 text-center border-0 shadow-sm p-3 bg-white rounded hover-shadow"> 
                    <div class="card-body d-flex flex-column justify-content-between"> 
                        <div>
                            <div class="p-3 bg-primary bg-opacity-10 rounded-circle d-inline-block mb-3">
                                <i class="fa-solid fa-book-open fa-3x text-primary"></i> 
                            </div>
                            <h4 class="card-title fw-bold text-dark">Catálogo de Libros</h4> 
                            <p class="text-muted small">Revisa el listado de cuentos, cambia sus portadas, edita sus datos generales y administra sus contenidos.</p>
                        </div>
                        <a href="ver-libros.php" class="btn btn-primary w-100 fw-bold mt-3 py-2">
                            <i class="fa-solid fa-eye me-1"></i> Ver todos los libros
                        </a> 
                    </div> 
                </div> 
            </div> 

            <div class="col-md-4 mb-4"> 
                <div class="card h-100 text-center border-0 shadow-sm p-3 bg-white rounded"> 
                    <div class="card-body d-flex flex-column justify-content-between"> 
                        <div>
                            <div class="p-3 bg-success bg-opacity-10 rounded-circle d-inline-block mb-3">
                                <i class="fa-solid fa-bookmark fa-3x text-success"></i> 
                            </div>
                            <h4 class="card-title fw-bold text-dark">Agregar Nuevo Libro</h4> 
                            <p class="text-muted small">Registra un cuento nuevo en la base de datos asignando su título, tiempo estimado de lectura y dificultad.</p>
                        </div>
                        <a href="nuevo-libro.php" class="btn btn-success w-100 fw-bold mt-3 py-2">
                            <i class="fa-solid fa-plus me-1"></i> Crear nuevo cuento
                        </a> 
                    </div> 
                </div> 
            </div> 

            <div class="col-md-4 mb-4"> 
                <div class="card h-100 text-center border-0 shadow-sm p-3 bg-white rounded"> 
                    <div class="card-body d-flex flex-column justify-content-between"> 
                        <div>
                            <div class="p-3 bg-warning bg-opacity-10 rounded-circle d-inline-block mb-3">
                                <i class="fa-solid fa-file-lines fa-3x text-warning"></i> 
                            </div>
                            <h4 class="card-title fw-bold text-dark">Agregar Páginas</h4> 
                            <p class="text-muted small">Redacta y añade nuevos fragmentos de lectura directamente a un libro específico ya existente.</p>
                        </div>
                        <a href="paginas-libro.php" class="btn btn-warning text-dark w-100 fw-bold mt-3 py-2">
                            <i class="fa-solid fa-file-plus me-1"></i> Crear nuevas páginas
                        </a> 
                    </div> 
                </div> 
            </div> 

        </div> 

    </div> 

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script> 
</body> 
</html>