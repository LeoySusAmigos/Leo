<?php

session_start();
include("php/conexion.php");

/*=========================================
=          VALIDAR SESIÓN
=========================================*/

if(!isset($_SESSION['userID'])){

    header("Location: login.php");
    exit();

}

$userID = $_SESSION['userID'];


/*=========================================
=          VALIDAR LECCIÓN
=========================================*/

if(!isset($_GET['leccion'])){

    header("Location: aventura-leo.php");
    exit();

}

$leccionID = (int)$_GET['leccion'];


/*=========================================
=      INFORMACIÓN DE LA LECCIÓN
=========================================*/

$sql = "

SELECT

    l.leccionID,
    l.numero,
    l.nombre,

    n.nivelID,
    n.nombre AS nombreNivel,
    n.vocal,
    n.orden

FROM leo_lecciones l

INNER JOIN leo_niveles n

ON l.nivelID = n.nivelID

WHERE l.leccionID = '$leccionID'

LIMIT 1

";

$leccion = $conn->query($sql)->fetch_assoc();

if(!$leccion){

    header("Location: aventura-leo.php");
    exit();

}


/*=========================================
=       PALABRAS DE LA LECCIÓN
=========================================*/

$sql = "

SELECT

    palabraID,
    silaba,
    palabra,
    imagen,
    audio,
    orden_palabra

FROM leo_palabras

WHERE leccionID='$leccionID'

ORDER BY orden_palabra ASC

";

$resultado = $conn->query($sql);

$palabras = [];

while($fila = $resultado->fetch_assoc()){

    $palabras[] = $fila;

}


/*=========================================
=       PROGRESO DEL USUARIO
=========================================*/

$sql = "

SELECT

    palabraID,
    fase,
    porcentaje

FROM leo_progreso

WHERE

    userID='$userID'

AND

    leccionID='$leccionID'

ORDER BY palabraID ASC

";

$resultadoProgreso = $conn->query($sql);

$progresos = [];

while($fila = $resultadoProgreso->fetch_assoc()){

    $progresos[] = $fila;

}
?>


<!DOCTYPE html>

<html lang="es">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1.0">

<title>

Lección con Leo

</title>

<link rel="shortcut icon" href="images/favicon/favicon-32x32.png" type="image/x-icon">

<link
rel="preconnect"
href="https://fonts.googleapis.com">

<link
rel="preconnect"
href="https://fonts.gstatic.com"
crossorigin>

<link
href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700&family=Quicksand:wght@500;700&display=swap"
rel="stylesheet">

<link
rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<link
rel="stylesheet"
href="styles/navbar.css">

<link rel="stylesheet" href="styles/leccion-leo.css">

</head>

<body>

<?php include 'components/navbar.php'; ?>

<main class="lesson-wrapper">

    <div class="lesson-header">
        <a href="aventura-leo.php" id="btnVolverNiveles" class="btn-home">

            <i class="fa-solid fa-house"></i>

        </a>

        <div class="lesson-info">

            <span class="lesson-level">

                Nivel <?php echo $leccion['orden']; ?>

                •

                Vocal <?php echo strtoupper($leccion['vocal']); ?>

            </span>

            <h1>

                Lección <?php echo $leccion['numero']; ?>

            </h1>

            <p>

                <?php echo htmlspecialchars($leccion['nombre']); ?>

            </p>

        </div>

        <div class="lesson-counter">

            Palabra

            <span id="numeroPalabra">

                1

            </span>

            de

            <span>

                <?php echo count($palabras); ?>

            </span>

        </div>

    </div>

    <div class="lesson-progress">

        <div
        class="progress-step active"
        id="step1">

            <div class="circle">

                1

            </div>

            <span>

                Escuchar

            </span>

        </div>

        <div class="progress-line"></div>

        <div
        class="progress-step"
        id="step2">

            <div class="circle">

                2

            </div>

            <span>

                Reconocer

            </span>

        </div>

        <div class="progress-line"></div>

        <div
        class="progress-step"
        id="step3">

            <div class="circle">

                3

            </div>

            <span>

                Producir

            </span>

        </div>

    </div>


    <div class="lesson-card">
        <div id="faseContainer">

            <div class="lesson-phase" id="fase1">

                <div class="leo-container">
                    <img src="images/aventuraLeo/leo-tronco.png" class="leo-personaje" alt="Leo">

                    <div class="leo-dialogo" id="dialogoFase1">

                        ¡Hola!

                        Escucha atentamente cómo suena esta sílaba.

                        Después la reconoceremos juntos.

                    </div>

                </div>
                <div class="lesson-buttons">
                    <button
                        id="btnEscuchar"
                        class="btn-audio"
                        data-audio="audios/LEO/<?php echo htmlspecialchars($palabras[0]['audio']); ?>">

                        <i class="fa-solid fa-volume-high"></i>

                        Escuchar

                    </button>

                


                    <button id="btnContinuar" class="btn-continuar" disabled>

                        Continuar
                        <i class="fa-solid fa-arrow-right"></i>

                    </button>
                </div>

                

            </div>

            <div class="lesson-phase" id="fase2" style="display:none;">

                <div class="leo-container">
                    <img src="images/aventuraLeo/leo-tronco.png" class="leo-personaje" alt="Leo">
                    <div class="leo-dialogo" id="dialogoFase2">
                        ¿Cuál de estas es la sílaba correcta?
                    </div>

                </div>

                <div class="opciones-silabas" id="opcionesSilabas">

                </div>

            </div>

            <div class="lesson-phase" id="fase3" style="display:none;">

                <div class="leo-container">

                    <img
                    src="images/aventuraLeo/leo-tronco.png"
                    class="leo-personaje"
                    alt="Leo">

                    <div
                    class="leo-dialogo"
                    id="dialogoFase3">

                        ¡Muy bien!

                        Ahora encontremos la palabra completa.

                    </div>

                </div>

                <div class="opciones-palabras" id="opcionesPalabras"></div>

                <div id="resultadoFinal" class="resultado-final" style="display:none;">

                    <div class="mensaje-final">

                        <i class="fa-solid fa-circle-check"></i>

                        ¡Excelente!

                        Ganaste +5 puntos ⭐

                    </div>

                    <div id="contenedorSiguiente" style="display:none;">

                        <button id="btnSiguientePalabra" class="btn-siguiente">

                            Siguiente palabra

                            <i class="fa-solid fa-arrow-right"></i>

                        </button>

                    </div>

                </div>

            </div>
            


        </div>

        <div class="lesson-left">

            <span class="titulo-silaba">

                Sílaba

            </span>

            <h2 id="silabaTexto">

                <?php echo htmlspecialchars($palabras[0]['silaba']); ?>

            </h2>

            <div class="imagen-palabra">

                <img id="imagenPrincipal" src="images/palabrasLeo/<?php echo htmlspecialchars($palabras[0]['imagen']); ?>" alt="">

            </div>

        </div>

    </div>

    <div id="modalSalir" class="modal-salir">

    <div class="modal-card">

        <h2>

            ¿Volver a los niveles?

        </h2>

        <p>

            Tu progreso ya guardado no se perderá.

            Podrás continuar esta aventura cuando quieras.

        </p>

        <div class="modal-botones">

            <button id="cancelarSalir" class="btn-modal-secundario">

                Seguir aprendiendo

            </button>

            <button id="confirmarSalir" class="btn-modal-principal">

                Volver a los niveles

            </button>

        </div>

    </div>

</div>

    
</main>


<script>
    const nivelID = <?php echo $leccion['nivelID']; ?>;
    const leccionID = <?php echo $leccion['leccionID']; ?>;
    const palabras = <?php echo json_encode($palabras, JSON_UNESCAPED_UNICODE); ?>;
    const progresosGuardados = <?php echo json_encode($progresos, JSON_UNESCAPED_UNICODE); ?>;

    let indiceActual = 0;

</script>

<script src="js/LeoFunciones.js"></script>

<script src="js/leccion-leo.js"></script>

</body>

</html>