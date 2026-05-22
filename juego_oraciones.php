<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Juego de Oraciones - Leo & Friends</title>
    <link rel="stylesheet" href="style_juego.css">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="game-container">
        <!-- Encabezado con info del cuento -->
        <header class="game-header">
            <h1>Libro: Ana y la manzana 🍎</h1>
            <p>Ordena las palabras para formar la oración</p>
        </header>

        <!-- Área visual inspirada en image_657102.jpg -->
        <div class="story-view">
            <img src="images/Finx.png" class="character-hint" alt="Guía">
            <div class="sentence-display" id="displayArea">
                <!-- Aquí aparecerán las palabras que el niño toque -->
                <span class="placeholder">Toca las palabras de abajo...</span>
            </div>
        </div>

        <!-- Panel de palabras desordenadas -->
        <div class="words-bank" id="wordsBank">
            <!-- Las palabras se generarán con JS -->
        </div>

        <!-- Controles -->
        <div class="controls">
            <button onclick="reinicioJuego()" class="btn-secondary">Reiniciar</button>
            <button onclick="verificarOracion()" class="btn-main">¡Listo!</button>
        </div>
    </div>

    <script src="js/juego.js"></script>
</body>
</html>