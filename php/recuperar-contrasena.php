<?php
session_start(); 
include("conexion.php"); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Recibimos los datos del formulario de nueva-contrasena.php
    $token = isset($_POST['token']) ? $_POST['token'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : ''; 
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : ''; 

    // Validar campos vacíos
    if (empty($token) || empty($password) || empty($confirm_password)) {
        header("Location: ../nueva-contrasena.php?token=" . $token . "&error=empty_fields");
        exit();
    }

    // Validar coincidencia de contraseñas
    if ($password !== $confirm_password) {
        header("Location: ../nueva-contrasena.php?token=" . $token . "&error=password_mismatch");
        exit();
    }
    
    // Verificar si el token es válido y vigente
    $hora_actual = date("Y-m-d H:i:s");
    $sql = "SELECT userID FROM usuarios WHERE token_recuperacion = ? AND token_expiracion > ?"; 
    
    $stmt = $conn->prepare($sql); 
    $stmt->bind_param("ss", $token, $hora_actual); 
    $stmt->execute(); 
    $resultado = $stmt->get_result(); 
    
    if ($usuario = $resultado->fetch_assoc()) { 
        
        // Encriptar la contraseña elegida por el papá
        $password_encriptada = password_hash($password, PASSWORD_BCRYPT); 
        
        // Actualizar la contraseña en la BD y limpiar el token
        $sql_update = "UPDATE usuarios SET password = ?, token_recuperacion = NULL, token_expiracion = NULL WHERE userID = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("si", $password_encriptada, $usuario['userID']);
        
        if ($stmt_update->execute()) {
            // Éxito: Redirigimos al Login clásico
            header("Location: ../login.html?recovery=success");
            exit();
        } else {
            die("Error crítico: No se pudo actualizar la contraseña.");
        }

    } else { 
        // Token inválido o expirado
        header("Location: ../nueva-contrasena.php?error=invalid_or_expired_token");
        exit();
    }
} else {
    header("Location: ../login.html");
    exit();
}
?>