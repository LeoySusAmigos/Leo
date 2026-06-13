<?php
session_start();
include("conexion.php"); // Asegúrate de que la ruta a tu conexión sea la correcta

// 1. SEGURIDAD: Solo el administrador puede procesar este formulario
if (!isset($_SESSION['userID']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

// 2. RECOGER LOS DATOS: Guardamos lo que viene del formulario en variables simples
$libro_id       = $_POST['libro_id'];
$numero_pagina  = $_POST['numero_pagina'];
$texto_pagina   = $_POST['texto_pagina'];

// 3. CONSULTA SQL: Insertar los datos directamente en tu tabla
$sql = "INSERT INTO paginas_libro (libro_id, numero_pagina, texto_pagina) 
        VALUES ('$libro_id', '$numero_pagina', '$texto_pagina')";

// 4. EJECUTAR Y COMPROBAR
if ($conn->query($sql) === TRUE) {
    // CORRECCIÓN: Redirige de vuelta a paginas-libro.php que es tu formulario real
    header("Location: ../admin/paginas-libro.php?status=success");
    exit();
} else {
    // Si hay un error en las columnas o tablas de MySQL, lo sabremos aquí
    echo "Error al guardar la página en la base de datos: " . $conn->error;
}
?>