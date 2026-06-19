<?php
session_start(); 
include("conexion.php"); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Limpiamos y validamos el correo
    $correo = filter_var($_POST['correo'], FILTER_SANITIZE_EMAIL); 
    
    if (empty($correo) || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../recuperar-contrasena.php?error=invalid_email");
        exit();
    }
    
    // Buscar si el correo existe en tu tabla 'usuarios'
    $sql = "SELECT userID FROM usuarios WHERE correo = ?"; 
    $stmt = $conn->prepare($sql); 
    $stmt->bind_param("s", $correo); 
    $stmt->execute(); 
    $resultado = $stmt->get_result(); 
    
    if ($usuario = $resultado->fetch_assoc()) { 
        
        // Generar token único y expiración (1 hora)
        $token = bin2hex(random_bytes(32)); 
        $expiracion = date("Y-m-d H:i:s", time() + 3600); 
        
        // Guardar token en tu tabla usuarios
        $sql_update = "UPDATE usuarios SET token_recuperacion = ?, token_expiracion = ? WHERE userID = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ssi", $token, $expiracion, $usuario['userID']);
        $stmt_update->execute();

        // Enlace mágico que apunta a nueva-contrasena.php en la raíz de tu proyecto
        $enlace = "../nueva-contrasena.php?token=" . $token;

        echo "
        <!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <title>Simulador de Correo - Leo & Friends</title>
            <link href='https://fonts.googleapis.com/css2?family=Quicksand:wght@500;700&display=swap' rel='stylesheet'>
            <style>
                body { background-color: #136327; font-family: 'Quicksand', sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
                .card { background: white; padding: 40px; border-radius: 16px; text-align: center; box-shadow: 0 8px 24px rgba(0,0,0,0.2); max-width: 450px; }
                h2 { color: #333; margin-bottom: 10px; }
                p { color: #666; font-size: 15px; line-height: 1.5; margin-bottom: 25px; }
                .btn { background-color: #ff9800; color: white; padding: 14px 28px; text-decoration: none; border-radius: 8px; font-weight: bold; display: inline-block; transition: background 0.2s; }
                .btn:hover { background-color: #e68a00; }
            </style>
        </head>
        <body>
            <div class='card'>
                <h2>¡Enlace de recuperación listo! 📬</h2>
                <p>Hola papá/mamá: Simulando el entorno local, haz clic en el siguiente botón para cambiar la contraseña de tu hijo.</p>
                <a href='$enlace' class='btn'>Establecer nueva contraseña</a>
            </div>
        </body>
        </html>";
        exit();

    } else { 
        // Si el correo del papá no está registrado
        header("Location: ../recuperar-contrasena.php?error=notfound");
        exit();
    }
} else {
    header("Location: ../recuperar-contrasena.php");
    exit();
}
?>