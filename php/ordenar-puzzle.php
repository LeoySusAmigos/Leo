<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require 'conexion.php';

$result = $conn->query("SELECT libro_id, titulo, portada FROM libros ORDER BY titulo ASC");

if (!$result) {
    echo json_encode(['error' => $conn->error]);
    exit;
}

$libros = [];
while ($row = $result->fetch_assoc()) {
    $row['portada_url'] = 'images/cuentos/' . $row['portada'];
    $libros[] = $row;
}

echo json_encode($libros);
$conn->close();
?>