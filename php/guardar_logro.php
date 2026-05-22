<?php
session_start();
include 'conexion.php'; // Asegúrate de que la ruta sea correcta

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificamos que el usuario esté logueado
    if (!isset($_SESSION['userID'])) {
        die("Sesión no iniciada");
    }

    $userID = $_SESSION['userID'];
    $actividad = mysqli_real_escape_string($conexion, $_POST['actividad']);
    $puntos = (int)$_POST['puntos'];

    // Insertamos el registro de que terminó el cuento
    $sql = "INSERT INTO progreso_juego (userID, actividad, puntuacion) 
            VALUES ('$userID', '$actividad', '$puntos')";

    if (mysqli_query($conexion, $sql)) {
        echo "¡Progreso guardado con éxito!";
    } else {
        echo "Error al guardar: " . mysqli_error($conexion);
    }
}
?>