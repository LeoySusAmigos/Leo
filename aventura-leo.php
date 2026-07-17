<?php

session_start();
include("php/conexion.php");

if(!isset($_SESSION['userID'])){
    header("Location: login.php");
    exit();
}

$userID = $_SESSION['userID'];


/*=========================================
=             DATOS DEL NIÑO
=========================================*/

$sql = "

SELECT

nombre_nino,
foto_nino

FROM usuarios

WHERE userID = '$userID'

";

$usuario = $conn->query($sql)->fetch_assoc();


/*=========================================
=           PROGRESO GENERAL
=========================================*/

$sql = "

SELECT

puntos,
modulo_actual,
nivel_actual,
leccion_actual

FROM progreso

WHERE userID = '$userID'

";

$progreso = $conn->query($sql)->fetch_assoc();


/*=========================================
=          NIVELES DE LEO
=========================================*/

$sql = "

SELECT

n.nivelID,
n.nombre,
n.vocal,
n.orden,

COALESCE(d.desbloqueado,0) AS desbloqueado

FROM leo_niveles n

LEFT JOIN leo_niveles_desbloqueo d

ON n.nivelID=d.nivelID

AND d.userID='$userID'

ORDER BY n.orden

";

$niveles = $conn->query($sql);

/*=========================================
=        LECCIONES DE CADA NIVEL
=========================================*/

$sql = "

SELECT

leccionID,
nivelID,
numero,
nombre

FROM leo_lecciones

ORDER BY nivelID, numero

";

$resLecciones = $conn->query($sql);

$leccionesPorNivel = [];

while($leccion = $resLecciones->fetch_assoc()){

    $leccionesPorNivel[$leccion['nivelID']][] = $leccion;

}



/*=========================================
=      PALABRAS DE CADA LECCIÓN
=========================================*/

$sql = "

SELECT

palabraID,
leccionID,
palabra,
silaba,
imagen,
audio,
orden_palabra

FROM leo_palabras

ORDER BY leccionID, orden_palabra

";

$resPalabras = $conn->query($sql);

$palabrasPorLeccion = [];

while($palabra = $resPalabras->fetch_assoc()){

    $palabrasPorLeccion[$palabra['leccionID']][] = $palabra;

}



/*=========================================
=   PALABRAS COMPLETADAS POR EL USUARIO
=========================================*/

$sql = "

SELECT

palabraID

FROM leo_palabras_completadas

WHERE userID='$userID'

";

$resCompletadas = $conn->query($sql);

$palabrasCompletadas = [];

while($fila = $resCompletadas->fetch_assoc()){

    $palabrasCompletadas[$fila['palabraID']] = true;

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

Aventura con Leo

</title>


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

<link
rel="stylesheet"
href="styles/aventura-leo.css">

</head>

<body>

<?php include 'components/navbar.php'; ?>

<div class="aventura-container">

    <!--==========================
            HEADER
    ===========================-->

    <header class="aventura-header">

        <div class="header-center-title">

            <h1>AVENTURA CON LEO</h1>

            <p>Aprende nuevas palabras jugando con Leo</p>

        </div>

    </header>


    <!--==========================
        TABS DE LAS MASCOTAS
    ===========================-->

    <div class="tabs-mascotas-container">

        <div class="tab-item active">

            <img
            src="images/Leito.png"
            class="tab-mascota-img">

            <div class="tab-text text-leo">

                <span class="tab-title">
                    Comprensión y vocabulario
                </span>

                <span class="tab-subtitle">
                    Leo
                </span>

            </div>

        </div>


        <a href="aventura-capy.php" class="tab-item">

            <img
            src="images/capy1.png"
            class="tab-mascota-img">

            <div class="tab-text text-capy">

                <span class="tab-title">
                    Gramática y oraciones
                </span>

                <span class="tab-subtitle">
                    Capy
                </span>

            </div>

        </a>


        <a href="biblioteca.php" class="tab-item">

            <img
            src="images/FinxHi.png"
            class="tab-mascota-img">

            <div class="tab-text text-finx">

                <span class="tab-title">
                    Cuentos
                </span>

                <span class="tab-subtitle">
                    Finx
                </span>

            </div>

        </a>

    </div>



    <!--==========================
        CONTENIDO
    ===========================-->

    <main class="content-wrapper-modern">



        <!--==========================
                BANNER
        ===========================-->

        <div class="info-banner-niveles">

            <div class="banner-icon-circle">

                <i class="fa-solid fa-book-open"></i>

            </div>

            <div class="banner-info-text">

                <strong>

                    ¡Aprender a leer nunca fue tan divertido!

                </strong>

                <p>

                    Completa las palabras de cada lección para dominar una vocal y desbloquear el siguiente nivel.

                </p>

            </div>

        </div>



        <!--==========================
             NIVELES
        ===========================-->

        <div class="niveles-container">
            <?php

                while($nivel = $niveles->fetch_assoc()){

                    $nivelID = $nivel['nivelID'];

                    $desbloqueado = (int)$nivel['desbloqueado'];

            ?>

            <div class="nivel-row-container <?php echo !$desbloqueado ? 'locked-level' : ''; ?>">

                <!--=========================
                        ENCABEZADO NIVEL
                =========================-->

                <div class="nivel-row-header">

                    <div class="nivel-badge-pill">

                        Nivel <?php echo $nivel['orden']; ?>

                    </div>

                    <span class="nivel-meta-info">

                        Vocal <?php echo strtoupper($nivel['vocal']); ?>

                    </span>

                    <?php if(!$desbloqueado){ ?>

                        <div class="nivel-lock">

                            <i class="fa-solid fa-lock"></i>

                        </div>

                    <?php } ?>

                </div>



                <!--=========================
                        LECCIONES
                =========================-->

                <div class="nivel-row-cards-flex">

                    <?php

                    if(isset($leccionesPorNivel[$nivelID])){

                        foreach($leccionesPorNivel[$nivelID] as $leccion){

                            $palabras = $palabrasPorLeccion[$leccion['leccionID']] ?? [];

                            $completadas = 0;

                    ?>

                    <div class="card-leccion-modern <?php echo !$desbloqueado ? 'locked' : ''; ?>">

                        <?php if($desbloqueado){ ?>

                        <a href="leccion-leo.php?leccion=<?php echo $leccion['leccionID']; ?>">

                        <?php } ?>

                            <div class="card-left-icon">

                                <i class="fa-solid fa-book-open"></i>

                            </div>

                            <div class="card-center-data">

                                <h4>

                                    Lección <?php echo $leccion['numero']; ?>

                                </h4>

                                <span class="lesson-name">

                                    <?php echo htmlspecialchars($leccion['nombre']); ?>

                                </span>

                                <div class="lesson-words">
                                    <?php

                                    foreach($palabras as $palabra){

                                        $aprendida = isset($palabrasCompletadas[$palabra['palabraID']]);

                                        if($aprendida){
                                            $completadas++;
                                        }

                                    ?>

                                        <div class="word-pill <?php echo $aprendida ? 'completed' : ''; ?>">

                                            <span class="word-check">

                                                <?php if($aprendida){ ?>

                                                    <i class="fa-solid fa-check"></i>

                                                <?php }else{ ?>

                                                    <span class="empty-circle"></span>

                                                <?php } ?>

                                            </span>

                                            <span class="word-text">

                                                <?php echo htmlspecialchars($palabra['palabra']); ?>

                                            </span>

                                        </div>

                                    <?php

                                    }

                                    ?>

                                </div>

                                <div class="lesson-progress">

                                    <?php

                                    echo $completadas;

                                    ?>

                                    /

                                    <?php

                                    echo count($palabras);

                                    ?>

                                    palabras aprendidas

                                </div>

                            </div>



                                <div class="card-right-status">

                                    <?php

                                    if($completadas == count($palabras) && count($palabras)>0){

                                    ?>

                                        <span class="status-check-circle completed">

                                            <i class="fa-solid fa-check"></i>

                                        </span>

                                    <?php

                                    }else{

                                    ?>

                                        <span class="status-check-circle empty"></span>

                                    <?php

                                    }

                                    ?>

                                </div>

                                <?php if($desbloqueado){ ?>

                                </a>

                                <?php } ?>

                    </div>

                    <?php

                            }

                        }

                    ?>

                </div>

            </div>

            <?php

            }

            ?>

        </div>

    </main>

</div>