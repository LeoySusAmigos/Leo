<?php
session_start();
include("conexion.php");

// 1. SEGURIDAD: Solo admin
if (!isset($_SESSION['userID']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

// 2. VERIFICAR QUE VENGA EL ID
if (isset($_GET['id'])) {
    $libro_id = $_GET['id'];

    // PASO A: Eliminar primero las páginas asociadas a este libro (evita error de Llave Foránea)
    $sqlPaginas = "DELETE FROM paginas_libro WHERE libro_id = ?";
    $stmtPaginas = $conn->prepare($sqlPaginas);
    $stmtPaginas->bind_param("i", $libro_id);
    $stmtPaginas->execute();
    $stmtPaginas->close();

    // PASO B: Ahora que el libro no tiene páginas amarradas, lo borramos de forma segura
    $sqlLibro = "DELETE FROM libros WHERE libro_id = ?";
    $stmtLibro = $conn->prepare($sqlLibro);
    $stmtLibro->bind_param("i", $libro_id);

    if ($stmtLibro->execute()) {
        // Redirige al dashboard principal de administración con aviso de éxito
        header("Location: ../admin/dashboard.php?status=book_deleted");
        exit();
    } else {
        echo "Error al eliminar el libro: " . $stmtLibro->error;
    }

    $stmtLibro->close();
} else {
    echo "ID de libro no proporcionado.";
}

$conn->close();
?>