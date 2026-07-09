<?php
session_start(); 
include("conexion.php"); 
 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
 
    $correo = $_POST['correo']; 
    $password = $_POST['password']; 
    
    $sql = "SELECT * FROM usuarios WHERE correo = ?"; 
    $stmt = $conn->prepare($sql); 
    $stmt->bind_param("s", $correo); 
    $stmt->execute(); 
    $resultado = $stmt->get_result(); 
    
    if ($usuario = $resultado->fetch_assoc()) { 
    
        if (password_verify($password, $usuario['password'])) { 
            $_SESSION['userID']      = $usuario['userID']; 
            $_SESSION['nombre_nino'] = $usuario['nombre_nino'];
            $_SESSION['nombre_papa'] = $usuario['nombre_papa'];
            $_SESSION['foto_nino']   = $usuario['foto_nino']; 
            $_SESSION['rol']         = $usuario['rol']; 
 
// REDIRECCIONES (IMPORTANTE POR LA CARPETA /php) 
 
            if ($usuario['rol'] == 'admin') { 
                header("Location: ../admin/dashboard.php"); 
            } else { 
                header("Location: ../inicio-nino.php"); 
            } 
            
            exit(); 
 
        } else {
            header("Location: ../login.html?error=password");
            exit();
        }
 
    } else { 
        header("Location: ../login.html?error=user");
        exit();
    }
} 
?>