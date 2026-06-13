<?php
session_start();
include("conexion.php");

// 1. SEGURIDAD: Solo admin
if (!isset($_SESSION['userID']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

// 2. VERIFICAR QUE VENGAN LOS DATOS
if (isset($_GET['id']) && isset($_GET['libro_id'])) {
    $pagina_id = $_GET['id'];
    $libro_id  = $_GET['libro_id'];

    // 3. CONSULTA DE ELIMINACIÓN
    $sql = "DELETE FROM paginas_libro WHERE pagina_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $pagina_id);

    if ($stmt->execute()) {
        // Regresa al panel de edición de ese libro con un estado de éxito
        header("Location: ../admin/editar-libro.php?id=" . $libro_id . "&status=page_deleted");
        exit();
    } else {
        echo "Error al eliminar la página: " . $stmt->error;
    }
    
    $stmt->close();
} else {
    echo "Faltan parámetros para eliminar la página.";
}

$conn->close();
?>