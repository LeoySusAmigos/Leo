<?php
session_start();
include("php/oraciones.php");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/juego-cuento2.css">

    <title>Juego de Letras</title>
</head>
<body>

    <h1>Completa la frase</h1>
    <p class="subtitulo">Arrastra la palabra correcta al espacio en blanco</p>

    <div class="frase-container" id="frase-container"></div>

    <div class="zona-drop" id="zona-drop">
        <span class="drop-hint">Arrastra aqui</span>
    </div>

    <div class="opciones-container" id="opciones-container"></div>

    <div id="mensaje"></div>

    <div class="bottom-panel">
        <button onclick="location.reload()">
            <img src="images/juego-cuento/retryButton.png">
        </button>
        <button id="botonPista" onclick="mostrarPista()">
            <img src="images/juego-cuento/ideaButton.png">
        </button>
    </div>

    <script>
        let oracionCorrecta = <?php echo json_encode($oracion); ?>;
        let fraseCompleta   = <?php echo json_encode($fila['frase']); ?>;
        let pista           = <?php echo json_encode($fila['pista1']); ?>;
        let pista2          = <?php echo json_encode($fila['pista2']); ?>;
        let opciones        = <?php echo json_encode($fila['opciones']); ?>;
    </script>

    <script src="js/juego-cuento2.js"></script>

</body>
</html>