<?php
session_start();

if (!isset($_SESSION['userID'])) {
    header('Content-Type: application/json');
    echo json_encode(["error" => "Usuario no autenticado"]);
    exit();
}

include 'conexion.php';

$userID = $_SESSION['userID'];

// OBTENER TODOS LOS LIBROS LEÍDOS

$libros_leidos = [];

$sql = "SELECT libro_id FROM progreso_libros WHERE userID = $userID";
$resultado = $conn->query($sql);

if ($resultado) {
    while ($fila = $resultado->fetch_assoc()) {
        $libros_leidos[] = $fila['libro_id'];
    }
}

// OBTENER TODOS LOS LIBROS

$sql = "SELECT * FROM libros ORDER BY nivel_id ASC, libro_id ASC";
$resultado = $conn->query($sql);

$libros = [];
$totalesPorNivel = [];
$leidosPorNivel = [];

// Guarda todos los libros y cuenta cuántos hay por nivel
while ($fila = $resultado->fetch_assoc()) {

    $nivel = $fila['nivel_id'];

    $libros[] = $fila;

    if (!isset($totalesPorNivel[$nivel])) {
        $totalesPorNivel[$nivel] = 0;
    }

    $totalesPorNivel[$nivel]++;

    if (in_array($fila['libro_id'], $libros_leidos)) {

        if (!isset($leidosPorNivel[$nivel])) {
            $leidosPorNivel[$nivel] = 0;
        }

        $leidosPorNivel[$nivel]++;
    }
}

$cuentos = [];

foreach ($libros as $fila) {

    $nivel = $fila['nivel_id'];

    // ¿Ya fue leído?
    $fila['leido'] = in_array($fila['libro_id'], $libros_leidos);

    // Nivel 1 siempre desbloqueado
    if ($nivel == 1) {

        $fila['bloqueado'] = false;

    } else {

        $nivelAnterior = $nivel - 1;

        $totalAnterior = $totalesPorNivel[$nivelAnterior] ?? 0;
        $leidosAnterior = $leidosPorNivel[$nivelAnterior] ?? 0;

        // Solo se desbloquea si TODOS los libros del nivel anterior fueron leídos
        $fila['bloqueado'] = ($leidosAnterior < $totalAnterior);
    }

    $cuentos[] = $fila;
}

header('Content-Type: application/json');
echo json_encode($cuentos);
?>