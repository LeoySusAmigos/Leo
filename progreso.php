<?php
session_start();

if (!isset($_SESSION['userID'])) {
    header("Location: register.html");
    exit();
}

include("php/conexion.php");

$nombre = $_SESSION['nombre_nino'];
$idUsuario = $_SESSION['userID'];

$sql = "SELECT * FROM progreso WHERE userID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();

$datos = $stmt->get_result()->fetch_assoc();

if (!$datos) {
    $datos = [
        'nivel' => 1,
        'porcentaje' => 0,
        'leccion_actual' => 1,
        'puntos' => 50,
        'racha' => 0
    ];
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Progreso del Niño</title>

    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/progreso.css">
</head>

<body>

    <div class="contenedor">

        <div class="barra-superior">

            <img src="images/hoja1.png" class="hoja hoja1" alt="">
            <img src="images/hoja2.png" class="hoja hoja2" alt="">

            <div class="titulo-barra">

                <a href="index.php">
                    <img src="images/Leo-1.png" alt="Camaleón">
                </a>

                <h2>Progreso</h2>

            </div>

        </div>

        <div class="contenido">

            <div class="tarjeta-principal">

                <div class="info-superior">

                    <div class="avatar">
                        <img src="images/nino.png" alt="Niño">
                    </div>

                    <div>
                        <h1>¡Hola, amiguito!</h1>
                        <p>Sigue aprendiendo y ganando logros.</p>
                    </div>

                </div>

                <p class="nivel-texto">
                    Nivel <?php echo $datos['nivel']; ?>
                </p>

                <div class="barra-progreso-fondo">

                    <div class="barra-progreso"
                        style="width: <?php echo $datos['porcentaje']; ?>%;">
                    </div>

                </div>

                <div class="texto-progreso">
                    <?php echo $datos['porcentaje']; ?>%
                </div>

            </div>

            <div class="estadisticas">

                <div class="card-figma">

                    <img src="images/libro.png" alt="">

                    <div>
                        <h3>Lección:</h3>
                        <p><?php echo $datos['leccion_actual']; ?></p>
                    </div>

                </div>

                <div class="card-figma">

                    <img src="images/coin.png" alt="">

                    <div>
                        <h3>Monedas:</h3>
                        <p><?php echo $datos['puntos']; ?></p>
                    </div>

                </div>

                <div class="card-figma">

                    <img src="images/mochila.png" alt="">

                    <div>
                        <h3>Racha:</h3>
                        <p><?php echo $datos['racha']; ?> días</p>
                    </div>

                </div>

            </div>

            <div class="avance">

                <h3 class="titulo-avance">Mi avance:</h3>

                <div class="contenedor-avance">

                    <div class="bloque-avance letras-box">

                        <h1>A</h1>

                        <div class="barra-abajo verde">
                            Letras ✔
                        </div>

                    </div>

                    <div class="bloque-avance silabas-box">

                        <h1>BA</h1>

                        <div class="barra-abajo amarilla">
                            Sílabas 🔒
                        </div>

                    </div>

                    <div class="bloque-avance palabras-box">

                        <h1>SOL</h1>

                        <div class="barra-abajo gris">
                            Palabras 🔒
                        </div>

                    </div>

                    <div class="bloque-avance palabras-box">

                        <h1>☁</h1>

                        <div class="barra-abajo gris">
                            Oraciones 🔒
                        </div>

                    </div>

                </div>

            </div>

            <h3 class="titulo-racha">Racha:</h3>

            <img src="images/finx2.png" class="gatito-superior" alt="Gatito">

            <div class="racha">

                <img src="images/mochila.png" class="mochila" alt="Mochila">

                <div class="texto-racha">

                    <h2>
                        ¡<?php echo $datos['racha']; ?> días de racha!
                    </h2>

                    <p>
                        Muy bien, comienza hoy tu primera racha.
                    </p>

                    <div class="circulos">

                        <span>⚪</span>
                        <span>⚪</span>
                        <span>⚪</span>
                        <span>⚪</span>
                        <span>⚪</span>

                    </div>

                </div>

                <img src="images/capy2.png" class="capibara" alt="Capibara">

            </div>

        </div>

    </div>

</body>

</html>