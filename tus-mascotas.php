<?php
session_start();

if (!isset($_SESSION['userID'])) {
    header("Location: register.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tus Mascotas</title>

    <link rel="stylesheet" href="styles/tus-mascotas.css">

    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Itim&display=swap" rel="stylesheet">
</head>
<body>

<div class="contenedor">

    <!-- BOTON VOLVER -->
    <a href="index.php" class="btn-volver">

    <img src="images/flecha.png" alt="Volver">

</a>

    <!-- TITULO -->
    <div class="titulo">

    <img src="images/cartel-mascotas.png" alt="">

    <h1>TUS MASCOTAS</h1>

</div>

    <!-- SUBTITULO -->
    <div class="subtitulo">
        Tu mascota te está esperando con una aventura
    </div>

    <!-- TARJETAS -->
    <div class="mascotas">

        <!-- LEO -->
        <div class="card verde">

            <div class="nombre leo">
                Leo
            </div>

            <img src="images/leo.png" alt="Leo">

            <a href="aventura1.html" class="btn-aventura btn verde">
                ⭐ Aventura 1
            </a>
        </div>

        <!-- CAPY -->
        <div class="card azul">

            <div class="nombre capy">
                Capy
            </div>

            <img src="images/capy3.png" alt="Capy">
            <a href="aventura2.php" class="btn-aventura btn-celeste">
                ⭐ Aventura 2
            </a>

        </div>

        <!-- FINX -->
        <div class="card amarillo">

            <div class="nombre finx">
                Finx
            </div>

            <img src="images/finx3.png" alt="Finx">
             <a href="biblioteca.php" class="btn-aventura btn- amarillo">
               ⭐ Mini Biblioteca
            </a>

        </div>

    </div>

</div>

</body>
</html>