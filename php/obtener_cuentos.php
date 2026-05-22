<?php
// Mandamos a llamar tu archivo de conexion.php
include 'conexion.php'; 

// Cambiado $conexion por $conn para que coincida exactamente con tu archivo
$sql = "SELECT * FROM cuentos ORDER BY orden";
$resultado = $conn->query($sql);

$cuentos = array();

if ($resultado) {
    while ($fila = $resultado->fetch_assoc()) {
        $cuentos[] = $fila;
    }
}

// Transformamos el resultado a JSON para el index.js
header('Content-Type: application/json');
echo json_encode($cuentos);
?>