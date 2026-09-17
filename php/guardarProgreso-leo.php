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
$porcentajeRecibido =
isset($data["porcentaje"])
? (int)$data["porcentaje"]
: null;


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

    if($porcentajeRecibido !== 99 && $porcentajeRecibido !== 100){

        echo json_encode([
            "success" => false
        ]);

        exit();

    }

    $porcentaje = $porcentajeRecibido;

}


$sql = "

SELECT progresoID, fase, porcentaje

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
$porcentajeAnterior = 0;

if($consulta->num_rows > 0){

    $fila = $consulta->fetch_assoc();
    $progresoID = (int)$fila["progresoID"];
    $faseAnterior = (int)$fila["fase"];
    $porcentajeAnterior = (int)$fila["porcentaje"];

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


if($fase === 3 && $porcentaje === 100 && $porcentajeAnterior < 100){

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

    $sql = "

    SELECT
        COUNT(*) AS totalLecciones,
        SUM(
            CASE
                WHEN NOT EXISTS(
                    SELECT 1
                    FROM leo_palabras p
                    LEFT JOIN leo_progreso pr
                    ON pr.palabraID=p.palabraID
                    AND pr.userID='$userID'
                    WHERE
                        p.leccionID=l.leccionID
                    AND (
                        pr.porcentaje IS NULL
                        OR pr.porcentaje < 100
                    )
                )
                THEN 1
                ELSE 0
            END
        ) AS leccionesCompletadas

    FROM leo_lecciones l
    WHERE
        l.nivelID='$nivelID'
    ";

    $resultadoNivel = $conn->query($sql);

    $nivel = $resultadoNivel->fetch_assoc();


    if(
        (int)$nivel["totalLecciones"] > 0
        &&
        (int)$nivel["totalLecciones"] ===
        (int)$nivel["leccionesCompletadas"]
    ){

        $sql = "
        SELECT
            nivelID
        FROM leo_niveles
        WHERE
            orden = (
                SELECT orden + 1
                FROM leo_niveles
                WHERE nivelID='$nivelID'
                LIMIT 1
            )
        LIMIT 1
        ";

        $resultadoSiguiente = $conn->query($sql);

        if($resultadoSiguiente->num_rows > 0){
            $siguienteNivel =
            $resultadoSiguiente->fetch_assoc();
            $siguienteNivelID =
            (int)$siguienteNivel["nivelID"];

            $sql = "
            SELECT
                nivelID
            FROM leo_niveles_desbloqueo

            WHERE
                userID='$userID'
            AND nivelID='$siguienteNivelID'

            LIMIT 1
            ";

            $resultadoDesbloqueo =
            $conn->query($sql);

            if($resultadoDesbloqueo->num_rows > 0){
                $sql = "
                UPDATE leo_niveles_desbloqueo
                SET desbloqueado=1

                WHERE
                    userID='$userID'
                AND nivelID='$siguienteNivelID'
                ";

                $conn->query($sql);
            }
            else{
                $sql = "
                INSERT INTO leo_niveles_desbloqueo(
                    userID,
                    nivelID,
                    desbloqueado
                )

                VALUES(
                    '$userID',
                    '$siguienteNivelID',
                    1
                )
                ";
                $conn->query($sql);
            }
        }
       }
}

echo json_encode([
    "success" => true,
    "fase" => $fase,
    "porcentaje" => $porcentaje
]);

?>