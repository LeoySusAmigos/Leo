<?php

session_start();

include("php/conexion.php");

if(!isset($_SESSION['userID'])){

    header("Location:login.php");
    exit();

}

$userID=$_SESSION['userID'];


/*=========================================
=           DATOS DEL NIÑO                =
=========================================*/

$sql="

SELECT
nombre_nino,
foto_nino

FROM usuarios

WHERE userID='$userID'

";

$usuario=$conn->query($sql);
$usuario=$usuario->fetch_assoc();


/*=========================================
=         PROGRESO GENERAL                =
=========================================*/

$sql="

SELECT
puntos,
modulo_actual,
nivel_actual,
leccion_actual,
porcentaje

FROM progreso

WHERE userID='$userID'

";

$progreso=$conn->query($sql);
$progreso=$progreso->fetch_assoc();


/*=========================================
=      NIVELES DE LEO                     =
=========================================*/

$sql="

SELECT

n.nivelID,

n.vocal,

n.nombre,

n.orden,

d.desbloqueado,

d.porcentaje

FROM leo_niveles n

LEFT JOIN leo_niveles_desbloqueo d

ON

n.nivelID=d.nivelID

AND

d.userID='$userID'

ORDER BY n.orden

";


$niveles=$conn->query($sql);


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
href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300;400;500;600;700&display=swap"
rel="stylesheet">

<link rel="stylesheet" href="styles/navbar.css">
<link
rel="stylesheet"
href="styles/aventura-leo.css">

<link
rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>

<body>
    <?php include 'components/navbar.php'; ?>

<div class="aventura">

    <div class="leo">

        <img

        src="images/aventuraLeo/leo-tronco.png"

        alt="Leo"

        >

    </div>

    <div class="globo">

        <img

        src="images/aventuraLeo/burbuja-dialogo.png"

        >

        <div class="texto-globo">

            <h2>

            ¡Hola,

            <?php

            echo $usuario['nombre_nino'];

            ?>

            !

            </h2>

            <p>

            ¿Qué nivel exploramos hoy?

            </p>

        </div>

    </div>

    <div class="mapa">
        <?php

        while($nivel=$niveles->fetch_assoc()){

            $porcentaje=$nivel['porcentaje']??0;

            $desbloqueado = isset($nivel['desbloqueado'])
            ? (int)$nivel['desbloqueado']
            : ($nivel['orden'] == 1 ? 1 : 0);

            $estrellas=0;

            if($porcentaje>=100){

                $estrellas=3;

            }

            elseif($porcentaje>=60){

                $estrellas=2;

            }

            elseif($porcentaje>0){

                $estrellas=1;

            }

        ?>

        <<div class="isla isla<?php echo $nivel['orden']; ?>
            <?php

            if(!$desbloqueado){

            echo " bloqueada";

            }

            ?>">

            <a

            <?php

            if($desbloqueado){

            ?>

            href="leccion-leo.php?nivel=<?php echo $nivel['nivelID']; ?>"

            <?php

            }

            ?>

            >

            <img src="images/aventuraLeo/isla<?php echo $nivel['vocal']; ?>.png" alt="">

            <div class="circulo">

                <?php echo $nivel['vocal']; ?>

            </div>

            <div class="nombre">
                Nivel <?php echo $nivel['orden']; ?>
            </div>

            <div class="estrellas">
                
                <?php
                for($i=1;$i<=3;$i++){

                    if($i <= $estrellas){
                        echo '<span class="estrella activa">★</span>';
                    }else{
                        echo '<span class="estrella">★</span>';
                    }

                }
                ?>

            </div>

            <?php if(!$desbloqueado){ ?>

            <div class="candado">
                <i class="fa-solid fa-lock"></i>
            </div>

            <?php } ?>

            </a>

        </div>

        <?php

        }

        ?>
    </div>

    <script src="js/aventura-leo.js"></script>

</div>

</body>

</html>