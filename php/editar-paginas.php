<?php
session_start();
include("conexion.php");

// 1. SEGURIDAD: Solo admin
if (!isset($_SESSION['userID']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

// 2. RECOGER DATOS
$pagina_id     = $_POST['pagina_id'];
$libro_id      = $_POST['libro_id']; // Lo ocupamos para saber a dónde regresar
$numero_pagina = $_POST['numero_pagina'];
$texto_pagina  = $_POST['texto_pagina'];

// 3. CONSULTA UPDATE
$sql = "UPDATE paginas_libro 
        SET numero_pagina = ?, texto_pagina = ? 
        WHERE pagina_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("isi", $numero_pagina, $texto_pagina, $pagina_id);

// 4. EJECUTAR Y REDIRIGIR
if ($stmt->execute()) {
    // Te regresa automáticamente a la suite de edición del libro con una alerta de éxito
    header("Location: ../admin/editar-libro.php?id=" . $libro_id . "&status=page_updated");
    exit();
} else {
    echo "Error al actualizar la página: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>