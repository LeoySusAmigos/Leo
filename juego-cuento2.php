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

    <h1>Ordena la oración</h1>

    <div class="container" id="container"></div>

    <div id="mensaje"></div>

    <script>

        let oracionCorrecta = <?php echo json_encode($oracion); ?>;

    </script>

    <script src="js/juego-cuento2.js"></script>

</body>
</html>