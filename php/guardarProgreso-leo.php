<?php

session_start();
include("conexion.php");

header("Content-Type: application/json");

if(!isset($_SESSION['userID'])){

    echo json_encode([
        "success" => false
    ]);

    exit();

}

$userID = $_SESSION['userID'];


$data = json_decode(
    file_get_contents("php://input"),
    true
);


$nivelID   = (int)$data["nivelID"];
$leccionID = (int)$data["leccionID"];
$palabraID = (int)$data["palabraID"];
$fase      = (int)$data["fase"];


if($fase < 1 || $fase > 3){

    echo json_encode([
        "success" => false
    ]);

    exit();

}

if($fase === 1){

    $porcentaje = 33;

}
elseif($fase === 2){

    $porcentaje = 66;

}
else{

    $porcentaje = 100;

}


$sql = "

SELECT progresoID, fase

FROM leo_progreso

WHERE

    userID='$userID'

AND nivelID='$nivelID'

AND leccionID='$leccionID'

AND palabraID='$palabraID'

LIMIT 1

";

$consulta = $conn->query($sql);


$faseAnterior = 0;

if($consulta->num_rows > 0){

    $fila = $consulta->fetch_assoc();
    $progresoID = (int)$fila["progresoID"];
    $faseAnterior = (int)$fila["fase"];

    $sql = "
    UPDATE leo_progreso
    SET
        fase='$fase',
        porcentaje='$porcentaje'
    WHERE
        progresoID='$progresoID'
    AND userID='$userID'
    ";
    $conn->query($sql);
}
else{
    $sql = "
    INSERT INTO leo_progreso(
        userID,
        nivelID,
        leccionID,
        palabraID,
        fase,
        porcentaje
    )
    VALUES(
        '$userID',
        '$nivelID',
        '$leccionID',
        '$palabraID',
        '$fase',
        '$porcentaje'
    )
    ";
    $conn->query($sql);
}


if($fase === 3 && $faseAnterior < 3){
    $sql = "
    UPDATE progreso
    SET
        puntos = puntos + 5,
        nivel_actual='$nivelID',
        leccion_actual='$leccionID'
    WHERE
        userID='$userID'
    LIMIT 1
    ";
    $conn->query($sql);
}

echo json_encode([
    "success" => true,
    "fase" => $fase,
    "porcentaje" => $porcentaje
]);

?>