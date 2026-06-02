<?php

include("conexion.php");

$sql = "SELECT * FROM oraciones ORDER BY RAND() LIMIT 1";

$resultado = mysqli_query($conn, $sql);

$fila = mysqli_fetch_assoc($resultado);

$oracion = $fila['oracion'];

?>