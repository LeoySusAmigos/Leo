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
    <link rel="shortcut icon" href="images/favicon/favicon-32x32.png" type="image/x-icon">
</head>

<body>

    <h1>Ordena la oración</h1>

    <div class="container" id="container"></div>

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
        let pista = <?php echo json_encode($fila['pista1']); ?>;
        let pista2 = <?php echo json_encode($fila['pista2']); ?>;
    </script>

    <script src="js/juego-cuento2.js"></script>

</body>
</html>