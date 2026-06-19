<?php
session_start();
include("conexion.php"); // Asegúrate de que la ruta a tu conexión sea la correcta

// 1. SEGURIDAD: Solo el administrador puede procesar este formulario
if (!isset($_SESSION['userID']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

// 2. RECOGER LOS DATOS DE TEXTO: Coinciden al 100% con tu nuevo-libro.php
$titulo          = $_POST['titulo'];
$tiempo_estimado = $_POST['tiempo_estimado']; 
$nivel_id        = $_POST['nivel']; // Recibe el valor numérico del select

// 3. PROCESAR LA IMAGEN DE PORTADA
$nombre_imagen   = $_FILES['portada']['name'];      
$ruta_temporal   = $_FILES['portada']['tmp_name'];  
$carpeta_destino = "../images/cuentos/" . $nombre_imagen; // IMPORTANTE: La carpeta 'cuentos' debe existir dentro de 'img'

// 4. MOVER ARCHIVO Y EJECUTAR CONSULTA
// Validamos primero si la imagen realmente se pudo mover a la carpeta destino
if (move_uploaded_file($ruta_temporal, $carpeta_destino)) {

    // Cambiado para usar exactamente tus columnas de la base de datos: tiempo_estimado y nivel_id
    $sql = "INSERT INTO libros (titulo, portada, tiempo_estimado, nivel_id) 
            VALUES ('$titulo', '$nombre_imagen', '$tiempo_estimado', '$nivel_id')";

    // 5. EJECUTAR Y COMPROBAR
    if ($conn->query($sql) === TRUE) {
        // Redirige de vuelta al formulario con éxito
        header("Location: ../admin/nuevo-libro.php?status=success");
        exit();
    } else {
        // Si hay algún error en el SQL, lo sabremos aquí
        echo "Error en la base de datos al guardar el libro: " . $conn->error;
    }

} else {
    // Este es el error que te apareció. Desaparecerá en cuanto la carpeta 'cuentos' exista.
    echo "Error: No se pudo subir la imagen de portada. Verifica que la ruta '../images/cuentos/' exista en tu servidor local.";
}
?>