<?php
include ("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $pass   = $_POST['password'];
    $confirm_pass = $_POST['confirm_password'];

    if ($pass !== $confirm_pass) {
        die("Las contraseñas no coinciden.");
    }

    $password = password_hash($pass, PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuarios (nombre_nino, correo, password, rol) VALUES (?, ?, ?, 'usuario')";

    $stmt = $conn->prepare($sql); 
    $stmt->bind_param("sss", $nombre, $correo, $password); 


    if ($stmt->execute()) {
    echo "Usuario registrado correctamente";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conexion);
    }
}
?>