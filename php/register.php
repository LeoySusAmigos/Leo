<?php
include ("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_nino = $_POST['nombre_nino'];
    $nombre_papa = $_POST['nombre_papa'];
    $correo = $_POST['correo'];
    $pass   = $_POST['password'];
    $confirm_pass = $_POST['confirm_password'];

    if ($pass !== $confirm_pass) {
        die("Las contraseñas no coinciden.");
    }

    $password = password_hash($pass, PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuarios (nombre_nino, nombre_papa, correo, password, rol) VALUES (?, ?, ?, ?, 'usuario')";

    $stmt = $conn->prepare($sql); 
    $stmt->bind_param("ssss", $nombre_nino, $nombre_papa, $correo, $password); 

    if ($stmt->execute()) {
        header("Location: ../index.php");
        exit();
    } else {
        echo "Error: " . $conn->connect_error;
    }

    $stmt->close();
    $conn->close();

}
?>