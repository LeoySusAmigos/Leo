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

Aventura Leo

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

    <div class="leito">
        <img src="images/aventuraLeo/leo-senalando.png" alt="Leo" class="leo-bg">
    </div>

    <header class="aventura-header">

        <div class="header-center-title">

            <h1>AVENTURA CON LEO</h1>

            <p>Aprende nuevas palabras jugando con Leo</p>

        </div>

    </header>

    <div class="tabs-mascotas-container">
            <a href="aventura-leo.php" class="tab-item active-leo">
                <img src="images/Leito.png" alt="Leo" class="tab-mascota-img">
                <div class="tab-text text-leo">
                    <span class="tab-title text-secondary">Comprensión y vocabulario</span>
                    <span class="tab-subtitle">Leo</span>
                </div>
            </a>    

            <a href="aventura2.php" class="tab-item">
                <img src="images/capy1.png" alt="Capy" class="tab-mascota-img">
                <div class="tab-text text-capy">
                    <span class="tab-title text-secondary">Gramática y oraciones</span>
                    <span class="tab-subtitle">Capy</span>
                </div>
            </a>
            
           <a href="biblioteca.php" class="tab-item">
                <img src="images/FinxHi.png" alt="Finx" class="tab-mascota-img">
                <div class="tab-text text-finx">
                    <span class="tab-title text-success">Cuentos</span>
                    <span class="tab-subtitle">Finx</span>
                </div>
           </a>

            
        </div>


    <main class="content-wrapper-modern">



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




        <div class="niveles-container">

            <?php while($nivel = $niveles->fetch_assoc()):

                $nivelID = $nivel['nivelID'];
                $desbloqueado = (int)$nivel['desbloqueado'];

                $totalPalabrasNivel = 0;
                $palabrasAprendidasNivel = 0;

                if(isset($leccionesPorNivel[$nivelID])){

                    foreach($leccionesPorNivel[$nivelID] as $lec){

                        if(isset($palabrasPorLeccion[$lec['leccionID']])){

                            foreach($palabrasPorLeccion[$lec['leccionID']] as $p){

                                $totalPalabrasNivel++;

                                if(isset($palabrasCompletadas[$p['palabraID']])){
                                    $palabrasAprendidasNivel++;
                                }

                            }

                        }

                    }

                }

            ?>

            <div class="nivel-row-container <?php echo !$desbloqueado ? 'locked-level' : ''; ?>">

                <!-- Encabezado del nivel -->

                <div class="nivel-row-header">

                    <div class="header-left">

                        <span class="nivel-badge-pill">
                            Nivel <?php echo $nivel['orden']; ?>
                        </span>

                        <div class="nivel-info">

                            <span class="vocal-title">
                                Vocal <?php echo strtoupper($nivel['vocal']); ?>
                            </span>

                            <span class="separador">•</span>

                            <span class="nivel-progreso">

                                <?php echo $palabrasAprendidasNivel; ?>

                                /

                                <?php echo $totalPalabrasNivel; ?>

                                palabras aprendidas

                            </span>

                        </div>

                    </div>

                    <div class="header-right">

                        <?php if(!$desbloqueado){ ?>

                            <i class="fa-solid fa-lock nivel-lock"></i>

                        <?php } ?>

                        <i class="fa-solid fa-chevron-down toggle-nivel"></i>

                    </div>

                </div>



                <div class="nivel-content">

                    <?php

                    if(isset($leccionesPorNivel[$nivelID])):

                    foreach($leccionesPorNivel[$nivelID] as $leccion):


                        $palabras = $palabrasPorLeccion[$leccion['leccionID']] ?? [];

                        $aprendidas = 0;

                        foreach($palabras as $p){

                            if(isset($palabrasCompletadas[$p['palabraID']])){
                                $aprendidas++;
                            }

                        }

                        $totalPalabras = count($palabras);

                        if($aprendidas == 0){

                            $textoBoton = "Comenzar aventura";
                            $iconoBoton = "fa-play";
                            $claseBoton = "btn-comenzar";

                        }
                        elseif($aprendidas < $totalPalabras){

                            $textoBoton = "Continuar aventura";
                            $iconoBoton = "fa-forward";
                            $claseBoton = "btn-continuar";

                        }
                        else{

                            $textoBoton = "Explorar de nuevo";
                            $iconoBoton = "fa-compass";
                            $claseBoton = "btn-explorar";

                        }

                    ?>

                    

                    <div class="leccion-card">

                        <div class="leccion-header">

                            <?php if($desbloqueado){ ?>

                            <a href="leccion-leo.php?leccion=<?php echo $leccion['leccionID']; ?>" class="leccion-link">

                            <?php } ?>

                                <div class="leccion-left">

                                    <div class="icono-leccion">

                                        <i class="fa-solid fa-book-open"></i>

                                    </div>

                                    <div>

                                        <h4>

                                            Lección <?php echo $leccion['numero']; ?>

                                        </h4>

                                        <span>

                                            <?php echo htmlspecialchars($leccion['nombre']); ?>

                                        </span>

                                    </div>

                                </div>

                            <?php if($desbloqueado){ ?>

                            </a>

                            <?php } ?>

                        </div>



                        <div class="palabras-grid">

                        <?php foreach($palabras as $palabra):

                            $completada = isset($palabrasCompletadas[$palabra['palabraID']]);

                        ?>

                        <div class="palabra-item <?php echo $completada ? 'completada' : ''; ?>">

                            <div class="palabra-check">

                                <?php if($completada){ ?>

                                    <i class="fa-solid fa-check"></i>

                                <?php }else{ ?>

                                    <span class="circulo-vacio"></span>

                                <?php } ?>

                            </div>

                            <span class="palabra-texto">
                                <?php echo htmlspecialchars($palabra['palabra']); ?>
                            </span>

                        </div>

            <?php endforeach; ?>

                        </div>

                        <div class="leccion-footer">

                            <span>

                                <?php echo $aprendidas; ?>

                                /

                                <?php echo count($palabras); ?>

                                palabras aprendidas

                            </span>

                             <?php if($desbloqueado){ ?>

                                <a
                                    href="leccion-leo.php?leccion=<?php echo $leccion['leccionID']; ?>"
                                    class="btn-aventura <?php echo $claseBoton; ?>">

                                    <i class="fa-solid <?php echo $iconoBoton; ?>"></i>

                                    <?php echo $textoBoton; ?>

                                </a>

                            <?php } ?>

                        </div>

                    </div>

                    <?php endforeach; endif; ?>

                </div>

            </div>

            <?php endwhile; ?>

        </div>

    </main>

</div>

<script src="js/aventura-leo.js"></script>

</body>
</html>