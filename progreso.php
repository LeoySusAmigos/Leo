<?php
session_start();

if (!isset($_SESSION['userID'])) {
    header("Location: register.html");
    exit();
}

include("php/conexion.php");

$nombre = $_SESSION['nombre_nino'];
$avatar_nino = $_SESSION['foto_nino'];
$idUsuario = $_SESSION['userID'];

$sql = "SELECT * FROM progreso WHERE userID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();

$datos = $stmt->get_result()->fetch_assoc();

if (!$datos) {
    $datos = [
        'nivel_actual'    => 0,
        'porcentaje'      => 0,
        'leccion_actual'  => 1,
        'puntos'          => 0,
        'racha'           => 0
    ];
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Progreso del Niño</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="styles/progreso.css">
    <link href="https://fonts.googleapis.com/css2?family=Balsamiq+Sans:wght@700&family=Fredoka:wght@600;900&family=Nunito:wght@700;900&family=Quicksand:wght@500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/navbar1.css">
</head>

<body>

    <?php include("navbar1.php"); ?>

    <div class="contenedor">

        <div class="contenido">

            <div class="encabezado-seccion">
                <span class="numero-seccion">1</span>
                <h2>Mi perfil</h2>
            </div>

            <div class="tarjeta-perfil">

                <div class="perfil-info">

                    <div class="avatar">
                        <div class="avatar-circle">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <?php echo $avatar_nino; ?>
                    </div>

                    <div>
                        <h1>Progreso de <?php echo $nombre; ?>.</h1>
                        <p class="subtitulo-perfil">
                            Nivel <?php echo $datos['nivel_actual']; ?> · Sigue aprendiendo y ganando logros
                        </p>
                    </div>

                </div>

            </div>

            <div class="encabezado-seccion">
                <span class="numero-seccion">2</span>
                <h2>Mi progreso</h2>
            </div>

            <div class="tarjeta-principal">

                <div class="progreso-top">
                    <p class="nivel-texto">
                        Nivel <?php echo $datos['nivel_actual']; ?> — <?php echo $datos['porcentaje']; ?>% completado
                    </p>
                    <span class="nota-animo">¡Sigue así!</span>
                </div>

                <div class="barra-progreso-fondo">
                    <div class="barra-progreso" style="width: <?php echo $datos['porcentaje']; ?>%;"></div>
                </div>

                <div class="texto-progreso">
                    <?php echo $datos['porcentaje']; ?>%
                </div>

                <div class="estadisticas">

                    <div class="card-figma">
                        <div class="icono-chip chip-azul">
                            <i class="fa-solid fa-book"></i>
                        </div>
                        <div>
                            <p class="stat-numero"><?php echo $datos['leccion_actual']; ?></p>
                            <h3>Lección actual</h3>
                        </div>
                    </div>

                    <div class="card-figma">
                        <div class="icono-chip chip-amarillo">
                            <i class="fa-solid fa-star"></i>
                        </div>
                        <div>
                            <p class="stat-numero"><?php echo $datos['puntos']; ?></p>
                            <h3>Puntos</h3>
                        </div>
                    </div>

                    <div class="card-figma">
                        <div class="icono-chip chip-naranja">
                            <i class="fa-solid fa-fire"></i>
                        </div>
                        <div>
                            <p class="stat-numero"><?php echo $datos['racha']; ?></p>
                            <h3>Días de racha</h3>
                        </div>
                    </div>

                </div>

                <h3 class="titulo-avance">Mapa de avance de habilidades</h3>

                <div class="mapa-habilidades">

                    <div class="linea-conexion"></div>

                    <div class="paso-habilidad completado">
                        <div class="circulo-paso">A</div>
                        <p>Letras</p>
                        <span class="estado">Completado</span>
                    </div>

                    <div class="paso-habilidad en-progreso">
                        <div class="circulo-paso">BA</div>
                        <p>Sílabas</p>
                        <span class="estado">En progreso</span>
                    </div>

                    <div class="paso-habilidad bloqueado">
                        <div class="circulo-paso">
                            <i class="fa-solid fa-lock text-muted"></i>
                        </div>
                        <p>Palabras</p>
                        <span class="estado">Bloqueado</span>
                    </div>

                    <div class="paso-habilidad bloqueado">
                        <div class="circulo-paso">
                            <i class="fa-solid fa-lock text-muted"></i>
                        </div>
                        <p>Oraciones</p>
                        <span class="estado">Bloqueado</span>
                    </div>

                </div>

            </div>

            <div class="encabezado-seccion">
                <span class="numero-seccion">3</span>
                <h2>Mi racha</h2>
            </div>

            <div class="racha">

                <img src="images/mochila.png" class="mochila" alt="Mochila">

                <div class="texto-racha">

                    <h2>
                        ¡<?php echo $datos['racha']; ?> días de racha!
                    </h2>

                    <p>
                        <?php echo $datos['racha'] > 0
                            ? "Sigue así para no perder tu racha."
                            : "Muy bien, comienza hoy tu primera racha."; ?>
                    </p>

                    <div class="circulos">
                        <?php
                        $dias = ['L', 'M', 'X', 'J', 'V', 'S', 'D'];
                        foreach ($dias as $i => $d) {
                            $activo = $i < $datos['racha'] ? 'activo' : '';
                            echo "<span class='dia-circulo $activo'>$d</span>";
                        }
                        ?>
                    </div>

                </div>

                <img src="images/capy3.png" class="capibara" alt="Capibara">

            </div>

        </div>

    </div>

</body>

</html>