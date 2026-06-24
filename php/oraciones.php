<?php

include("conexion.php");

$sql = "SELECT * FROM oraciones WHERE pagina_id = 1 ORDER BY RAND() LIMIT 1";
$resultado = mysqli_query($conn, $sql);

$fila = mysqli_fetch_assoc($resultado);

if (!$fila) {
    $fila = [
        "oracion" => "Sin oración disponible",
        "pista1" => "Sin pista",
        "pista2" => "Sin pista"
    ];
}

$oracion = $fila['oracion'];

?>