<?php
session_start();

if (!isset($_SESSION['userID'])) {
    header("Location: register.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personaliza tu Experiencia</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="styles/cuestionario.css">
    <link href="https://fonts.googleapis.com/css2?family=Balsamiq+Sans:wght@700&family=Fredoka:wght@600;900&family=Nunito:wght@700;900&family=Quicksand:wght@500;700;900&display=swap" rel="stylesheet">
</head>

<body>
    <div class="contenedor-cuestionario">
        <div class="barra-progreso">
            <span class="paso" id="paso-1">1</span>
            <span class="paso" id="paso-2">2</span>
            <span class="paso" id="paso-3">3</span>
            <span class="paso" id="paso-4">4</span>
            <span class="paso" id="paso-5">5</span>
        </div>

        <div class="subtitulo-contenedor">
            <i class="fa-solid fa-person-breastfeeding" style="color: rgb(99, 230, 190);"></i>
            <h3 class="subtitulo" id="texto-subtitulo">Primero, unas preguntas para <?php echo htmlspecialchars(isset($_SESSION['nombre_papa']) ? $_SESSION['nombre_papa'] : 'mamá o papá'); ?></h3>
        </div>
        
        <p class="pregunta" id="texto-pregunta">Cargando pregunta...</p>
    
        <div class="contenedor-opciones" id="contenedor-opciones"></div>

    </div>

    <script src="js/cuestionario.js"></script>
</body>
</html>