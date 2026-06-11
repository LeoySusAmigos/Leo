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
// No ponemos 'pagina_id' porque tu base de datos lo crea solo (Auto Increment)
$sql = "INSERT INTO paginas_libro (libro_id, numero_pagina, texto_pagina) 
        VALUES ('$libro_id', '$numero_pagina', '$texto_pagina')";

// 4. EJECUTAR Y COMPROBAR: Si se guarda con éxito, regresamos al formulario
if ($conn->query($sql) === TRUE) {
    // Redirige de vuelta con un mensaje de éxito en la URL
    header("Location: ../admin/agregar-pagina.php?status=success");
    exit();
} else {
    // Si hay un error, lo muestra en pantalla para saber qué pasó
    echo "Error al guardar la página: " . $conn->error;
}
?>