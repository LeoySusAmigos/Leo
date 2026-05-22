<?php 
session_start(); 
include("php/conexion.php"); 

// Proteger la página 
if (!isset($_SESSION['userID'])) { 
    header("Location: login.html"); 
    exit(); 
} 

// Obtener datos del usuario 
$id = $_SESSION['userID']; 

$sql = "SELECT nombre_nino, correo, rol FROM usuarios WHERE userID = ?"; 
$stmt = $conn->prepare($sql); 
$stmt->bind_param("i", $id); 
$stmt->execute(); 
$resultado = $stmt->get_result(); 
$usuario = $resultado->fetch_assoc(); 
?> 


<!DOCTYPE html> 
<html lang="en"> 
<head> 
    <meta charset="UTF-8"> 
    <title>Perfil</title> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"> 
</head> 
<body class="bg-light"> 

<div class="container mt-5"> 

    <div class="card shadow p-4"> 
        <h3 class="mb-4">Perfil del Usuario</h3> 

        <p><strong>Nombre:</strong> <?php echo $usuario['nombre_nino']; ?></p> 
        <p><strong>Correo:</strong> <?php echo $usuario['correo']; ?></p> 
        <p><strong>Rol:</strong> <?php echo $usuario['rol']; ?></p> 
        
        <a href="index.php" class="btn btn-secondary mt-3">Volver</a> 
    </div> 

</div> 
</body> 
</html>