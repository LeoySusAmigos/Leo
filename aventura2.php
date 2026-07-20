<?php
session_start();

if (!isset($_SESSION['userID'])) {
    header("Location: register.html");
    exit();
}

include 'php/conexion.php';
$userID = $_SESSION['userID'];

$libros_leidos = array();
$sql_progreso = "SELECT libro_id FROM progreso_libros WHERE userID = $userID";
$res_progreso = mysqli_query($conn, $sql_progreso);

if ($res_progreso) {
    while ($progreso = mysqli_fetch_assoc($res_progreso)) {
        $libros_leidos[] = $progreso['libro_id'];
    }
}

$sql_libros = "SELECT * FROM libros ORDER BY nivel_id ASC, libro_id ASC";
$res_libros = mysqli_query($conn, $sql_libros);

if (!$res_libros) {
    die("Error en la consulta: " . mysqli_error($conn));
}

$totalesPorNivel = [];
$leidosPorNivel = [];

$sql = "SELECT nivel_id, COUNT(*) AS total
        FROM libros
        GROUP BY nivel_id";

$res = mysqli_query($conn, $sql);

while ($fila = mysqli_fetch_assoc($res)) {
    $totalesPorNivel[$fila['nivel_id']] = $fila['total'];
}

$sql = "SELECT l.nivel_id, COUNT(*) AS leidos
        FROM progreso_libros p
        INNER JOIN libros l ON l.libro_id = p.libro_id
        WHERE p.userID = $userID
        GROUP BY l.nivel_id";

$res = mysqli_query($conn, $sql);

while ($fila = mysqli_fetch_assoc($res)) {
    $leidosPorNivel[$fila['nivel_id']] = $fila['leidos'];
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gramática con Capy</title>
    <link rel="stylesheet" href="styles/navbar.css">
    <link rel="stylesheet" href="styles/aventura2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>

    <?php include 'components/navbar.php'; ?>

    <div class="mapa-contenedor">
        <img src="images/fondo.png" alt="Mapa de Capibaras" class="mapa-fondo">

        <a href="juego-palabras.php" class="boton-nivel nivel-1" title="Ir al Nivel 1">
            <span class="texto-nivel">Nivel 1</span>
            <div class="piedra-asset"></div>
            <div class="estrellas">
                <span class="estrella activa">★</span>
                <span class="estrella activa">★</span>
                <span class="estrella activa">★</span>
            </div>
        </a>
        
        <a href="juegos.php?nivel=2" class="boton-nivel nivel-2" title="Ir al Nivel 2">
            <span class="texto-nivel">Nivel 2</span>
            <div class="piedra-asset"></div>
            <div class="estrellas">
                <span class="estrella activa">★</span>
                <span class="estrella activa">★</span>
                <span class="estrella activa">★</span>
            </div>
        </a>
        
        <a href="juegos.php?nivel=3" class="boton-nivel nivel-3" title="Ir al Nivel 3">
            <span class="texto-nivel">Nivel 3</span>
            <div class="piedra-asset"></div>
            <div class="estrellas">
                <span class="estrella activa">★</span>
                <span class="estrella activa">★</span>
                <span class="estrella activa">★</span>
            </div>
        </a>
        
        <a href="juegos.php?nivel=4" class="boton-nivel nivel-4" title="Ir al Nivel 4">
            <span class="texto-nivel">Nivel 4</span>
            <div class="piedra-asset"></div>
            <div class="estrellas">
                <span class="estrella activa">★</span>
                <span class="estrella activa">★</span>
                <span class="estrella activa">★</span>
            </div>
        </a>
        
        <a href="juegos.php?nivel=5" class="boton-nivel nivel-5" title="Ir al Nivel 5">
            <span class="texto-nivel">Nivel 5</span>
            <div class="piedra-asset"></div>
            <div class="estrellas">
                <span class="estrella activa">★</span>
                <span class="estrella activa">★</span>
                <span class="estrella activa">★</span>
            </div>
        </a>
    </div>

    <script src="js/navbar.js"></script>
</body>
</html>