<?php
session_start();

// Validamos que la sesión del niño esté activa
if (!isset($_SESSION['userID'])) {
    header('Content-Type: application/json');
    echo json_encode(array("error" => "Usuario no autenticado"));
    exit();
}

include 'conexion.php'; 
$userID = $_SESSION['userID']; //

// ==========================================
// PASO 1: OBTENER LOS CUENTOS QUE EL NIÑO YA LEYÓ
// ==========================================
$libros_leidos = array();
$sql_progreso = "SELECT libro_id FROM progreso_libros WHERE userID = $userID";
$res_progreso = $conn->query($sql_progreso);

if ($res_progreso) {
    while ($progreso = $res_progreso->fetch_assoc()) {
        $libros_leidos[] = $progreso['libro_id']; // Guarda una lista simple de IDs leídos
    }
}

// ==========================================
// PASO 2: TRAER TODOS LOS LIBROS ORDENADOS POR NIVEL
// ==========================================
// Cambié "cuentos" por "libros" para que use tu nueva estructura
$sql_libros = "SELECT * FROM libros ORDER BY nivel_id ASC, libro_id ASC";
$res_libros = $conn->query($sql_libros);

$cuentos = array();
$id_libro_anterior = 0; // Nos ayuda a verificar si el cuento anterior fue completado

if ($res_libros) {
    while ($fila = $res_libros->fetch_assoc()) {
        $libro_id = $fila['libro_id'];
        
        // REGLA 1: ¿Ya está leído por este niño?
        $ya_leido = in_array($libro_id, $libros_leidos);
        
        // REGLA 2: ¿Está bloqueado? 
        // Si no es el primer libro de todos ($id_libro_anterior != 0) 
        // y el libro anterior NO está en la lista de leídos, entonces se bloquea.
        $esta_bloqueado = false;
        if ($id_libro_anterior != 0 && !in_array($id_libro_anterior, $libros_leidos)) {
            $esta_bloqueado = true;
        }
        
        // Inyectamos estas dos nuevas variables directo en la información del libro
        $fila['leido'] = $ya_leido; 
        $fila['bloqueado'] = $esta_bloqueado;
        
        // Guardamos el libro modificado en nuestro arreglo principal
        $cuentos[] = $fila;
        
        // El libro actual se convierte en el "anterior" para la siguiente vuelta del ciclo
        $id_libro_anterior = $libro_id;
    }
}

// Transformamos todo el resultado a JSON para que index.js lo reciba idéntico a antes
header('Content-Type: application/json');
echo json_encode($cuentos);
?>