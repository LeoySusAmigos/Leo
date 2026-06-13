<?php
session_start();
include("conexion.php"); // Asegúrate de que la ruta a tu conexión sea la correcta

// 1. SEGURIDAD: Solo el administrador puede procesar este formulario
if (!isset($_SESSION['userID']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

// 2. RECOGER LOS DATOS DEL FORMULARIO
$libro_id        = $_POST['libro_id'];
$titulo          = $_POST['titulo'];
$tiempo_estimado = $_POST['tiempo_estimado'];
$nivel_id        = $_POST['nivel_id'];

// 3. COMPROBAR SI EL USUARIO SUBIÓ UNA NUEVA PORTADA
if (isset($_FILES['portada']) && $_FILES['portada']['error'] == 0) {
    
    // Si subió una imagen, preparamos la ruta de destino exacta que me indicaste
    $nombre_imagen   = $_FILES['portada']['name'];
    $ruta_temporal   = $_FILES['portada']['tmp_name'];
    $carpeta_destino = "../images/cuentos/" . $nombre_imagen;

    // Intentamos mover el archivo físico a la carpeta
    if (move_uploaded_file($ruta_temporal, $carpeta_destino)) {
        
        // CASO A: Actualización incluyendo la nueva imagen de portada
        $sql = "UPDATE libros 
                SET titulo = ?, portada = ?, tiempo_estimado = ?, nivel_id = ? 
                WHERE libro_id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssiii", $titulo, $nombre_imagen, $tiempo_estimado, $nivel_id, $libro_id);
        
    } else {
        echo "Error: No se pudo subir la nueva imagen de portada. Verifica los permisos de la carpeta '../images/cuentos/'.";
        exit();
    }

} else {
    // CASO B: El usuario no subió foto. Actualizamos todo MENOS la columna 'portada'
    $sql = "UPDATE libros 
            SET titulo = ?, tiempo_estimado = ?, nivel_id = ? 
            WHERE libro_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siii", $titulo, $tiempo_estimado, $nivel_id, $libro_id);
}

// 4. EJECUTAR CONSULTA Y REDIRECCIONAR
if ($stmt->execute()) {
    // Si todo sale bien, regresa a la pantalla de edición pasándole el ID del libro y un estado de éxito
    header("Location: ../admin/editar-libro.php?id=" . $libro_id . "&status=updated");
    exit();
} else {
    // Si ocurre un error con MySQL (llave foránea, etc), lo mostrará aquí de forma segura
    echo "Error al actualizar los datos del libro: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>