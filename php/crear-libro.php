<?php
session_start();
include("conexion.php"); // Asegúrate de que la ruta a tu conexión sea la correcta

// 1. SEGURIDAD: Solo el administrador puede procesar este formulario
if (!isset($_SESSION['userID']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

// 2. RECOGER LOS DATOS DE TEXTO: Guardamos los inputs en variables simples
$titulo              = $_POST['titulo'];
$tiempo_estimado_min = $_POST['tiempo_estimado_min'];
$nivel               = $_POST['nivel'];

// 3. PROCESAR LA IMAGEN DE PORTADA
$nombre_imagen = $_FILES['imagen_url']['name'];      // Nombre original del archivo (ej: 'arana.png')
$ruta_temporal = $_FILES['imagen_url']['tmp_name'];  // Dónde está guardado temporalmente por PHP
$carpeta_destino = "../img/libros/" . $nombre_imagen; // Ruta final donde queremos guardarlo

// Movemos el archivo de la carpeta temporal a nuestra carpeta real de imágenes
move_uploaded_file($ruta_temporal, $carpeta_destino);


// 4. CONSULTA SQL: Insertar los datos directamente en tu tabla 'libros'
// Guardamos la variable $nombre_imagen en la columna 'imagen_url'
$sql = "INSERT INTO libros (titulo, tiempo_estimado_min, nivel, imagen_url) 
        VALUES ('$titulo', '$tiempo_estimado_min', '$nivel', '$nombre_imagen')";


// 5. EJECUTAR Y COMPROBAR
if ($conn->query($sql) === TRUE) {
    // Redirige de vuelta al formulario con un mensaje de éxito en la URL
    header("Location: ../admin/agregar-libro.php?status=success");
    exit();
} else {
    // Si hay un error, lo muestra en pantalla para saber qué falló
    echo "Error al guardar el libro: " . $conn->error;
}
?>