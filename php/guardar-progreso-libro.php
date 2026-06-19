<?php
session_start();

// 1. Validar que el usuario tenga la sesión activa
if (!isset($_SESSION['userID'])) {
    die("Error: Usuario no autenticado");
}

// 2. Incluir la conexión a la base de datos
include 'conexion.php'; 

$userID = $_SESSION['userID'];

// 3. Recibir el ID del libro que se acaba de leer (enviado desde la pantalla del cuento)
if (isset($_GET['id'])) {
    $libroId = intval($_GET['id']); // Convertimos a número por seguridad básica
    
    $sql = "INSERT IGNORE INTO progreso_libros (userID, libro_id) VALUES ($userID, $libroId)";
    mysqli_query($conn, $sql);
    
    // 4. Redirigir al niño de vuelta a la biblioteca de Finx
    header("Location: ../biblioteca.php");
    exit();

} else {
    // Si entran al archivo sin mandar un ID de libro, los regresamos
    header("Location: ../biblioteca.php");
    exit();
}
?>