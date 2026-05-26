<?php
$puntos = 0;
$racha = 0;
$nivel = 1;
$progreso = 0;
?>

<?php
session_start();

if (!isset($_SESSION['userID'])) {
    header("Location: login.html");
    exit();
}

include("php/conexion.php");

$nombre = $_SESSION['nombre_nino'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Progreso</title>
    <link rel="stylesheet" href="styles/progreso.css">
</head>
<body>

<body>

<div class="contenedor">

    <div class="saludo-container">

        <div class="foto-perfil">
            <img src="images/nino.png" alt="Foto de perfil">
        </div>

        <div class="info-usuario">
            <h1>¡Hola, <?php echo htmlspecialchars($nombre); ?>!</h1>

            <p class="mensaje">
                Sigue aprendiendo y ganando logros.
            </p>
        </div>

    </div>

    <div class="barra-progreso">
        <div class="relleno"></div>
    </div>

<p class="porcentaje">0% completado</p>


</div>
<div class="tarjetas">
     <div class="tarjeta">
        <h3>📚 Lección</h3>
        <p>1</p>
    </div>

     <div class="tarjeta">
        <h3>⭐ Puntos</h3>
        <p>0</p>
    </div>

     <div class="tarjeta">
        <h3>🔥 Racha</h3>
        <p>0 días</p>
    </div>

</div>


</body>
</html>
