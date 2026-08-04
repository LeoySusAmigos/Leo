<?php

session_start();
include("conexion.php");

header("Content-Type: application/json");

if(!isset($_SESSION['userID'])){

    echo json_encode([
        "success"=>false
    ]);

    exit();

}

$userID = $_SESSION['userID'];

$data = json_decode(file_get_contents("php://input"), true);

$nivelID   = (int)$data["nivelID"];
$leccionID = (int)$data["leccionID"];
$palabraID = (int)$data["palabraID"];



/*=========================================
=      ¿YA EXISTE ESA PALABRA?
=========================================*/

$sql = "

SELECT progresoID

FROM leo_progreso

WHERE

userID='$userID'

AND nivelID='$nivelID'

AND leccionID='$leccionID'

AND palabraID='$palabraID'

LIMIT 1

";

$consulta = $conn->query($sql);



if($consulta->num_rows>0){

    $sql="

    UPDATE leo_progreso

    SET

        fase=3,
        porcentaje=100

    WHERE

        userID='$userID'

    AND palabraID='$palabraID'

    ";

    $conn->query($sql);

}

else{

    $sql="

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
        3,
        100

    )

    ";

    $conn->query($sql);

}



/*=========================================
=       ACTUALIZAR PROGRESO GENERAL
=========================================*/

$sql="

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



echo json_encode([

    "success"=>true

]);

?>