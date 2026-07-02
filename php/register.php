<?php
include ("conexion.php");
 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_nino   = $_POST['nombre_nino'];      
    $nombre_papa   = $_POST['nombre_papa'];  
    $correo        = $_POST['correo'];
    $pass          = $_POST['password'];
    $confirm_pass  = $_POST['confirm_password'];
 
    if ($pass !== $confirm_pass) {
        die("Contraseña incorrecta.");
    }

    $password = password_hash($pass, PASSWORD_DEFAULT);
 
    $sql = "INSERT INTO usuarios (nombre_nino, nombre_papa, correo, password, rol)
            VALUES (?, ?, ?, ?, 'usuario')";
 
    $stmt = $conn->prepare($sql);

    $stmt->bind_param("ssss", $nombre_nino, $nombre_papa, $correo, $password);
 
    if ($stmt->execute()) {
        session_start();

        $_SESSION['userID'] = $stmt->insert_id;
        $_SESSION['nombre_nino'] = $nombre_nino;
        $_SESSION['nombre_papa'] = $nombre_papa;
        $_SESSION['foto_nino'] = null;
        $_SESSION['rol'] = 'usuario';
        
        header("Location: ../cuestionario.php");
        exit();
        
    } else {
        echo "Error: " . $stmt->error;
    }
 
    $stmt->close();
    $conn->close();
}
?>