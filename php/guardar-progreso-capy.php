<?php

session_start();

header(
    'Content-Type: application/json; charset=utf-8'
);

function responder(
    $success,
    $message = '',
    $datos = []
) {

    echo json_encode(
        array_merge(
            [
                'success' => $success,
                'message' => $message
            ],
            $datos
        ),
        JSON_UNESCAPED_UNICODE
    );

    exit;
}

if (!isset($_SESSION['userID'])) {

    responder(
        false,
        'Usuario no identificado.'
    );

}

$userID =
    intval(
        $_SESSION['userID']
    );

require_once 'conexion.php';

$contenido =
    file_get_contents(
        'php://input'
    );

$datos =
    json_decode(
        $contenido,
        true
    );


if (!is_array($datos)) {

    $datos = $_POST;

}


if (
    !is_array($datos) ||
    empty($datos)
) {

    responder(
        false,
        'No se recibieron datos válidos.'
    );

}

$leccionID =
    isset($datos['leccion_id'])
        ? intval($datos['leccion_id'])
        : 0;

$actividadActual =
    isset($datos['actividad_actual'])
        ? intval($datos['actividad_actual'])
        : 1;

$porcentaje =
    isset($datos['porcentaje'])
        ? intval($datos['porcentaje'])
        : 0;

$puntos =
    isset($datos['puntos'])
        ? intval($datos['puntos'])
        : 0;

$completada =
    isset($datos['completada'])
        ? intval($datos['completada'])
        : 0;


if ($leccionID <= 0) {

    responder(
        false,
        'La lección no es válida.'
    );

}


$actividadActual =
    max(
        1,
        $actividadActual
    );


$porcentaje =
    max(
        0,
        min(
            100,
            $porcentaje
        )
    );


$puntos =
    max(
        0,
        $puntos
    );


$completada =
    $completada === 1
        ? 1
        : 0;

$sqlVerificar = "
    SELECT
        id,
        nivel
    FROM capy_lecciones
    WHERE id = ?
      AND activa = 1
    LIMIT 1
";


$stmtVerificar =
    $conn->prepare(
        $sqlVerificar
    );


if (!$stmtVerificar) {

    responder(
        false,
        'No se pudo verificar la lección.'
    );

}


$stmtVerificar->bind_param(
    'i',
    $leccionID
);


$stmtVerificar->execute();


$resultadoVerificar =
    $stmtVerificar->get_result();


$leccion =
    $resultadoVerificar->fetch_assoc();


$stmtVerificar->close();


if (!$leccion) {

    responder(
        false,
        'La lección no existe o está desactivada.'
    );

}

$sqlActividades = "
    SELECT
        COUNT(*) AS total
    FROM capy_actividades
    WHERE leccion_id = ?
      AND activa = 1
";


$stmtActividades =
    $conn->prepare(
        $sqlActividades
    );


if (!$stmtActividades) {

    responder(
        false,
        'No se pudo comprobar la lección.'
    );

}


$stmtActividades->bind_param(
    'i',
    $leccionID
);


$stmtActividades->execute();


$resultadoActividades =
    $stmtActividades->get_result();


$filaActividades =
    $resultadoActividades->fetch_assoc();


$totalActividades =
    intval(
        $filaActividades['total'] ?? 0
    );


$stmtActividades->close();


if ($totalActividades <= 0) {

    responder(
        false,
        'La lección no tiene actividades activas.'
    );

}


$actividadActual =
    min(
        $actividadActual,
        $totalActividades
    );

if ($completada === 1) {

    $actividadActual =
        $totalActividades;

    $porcentaje =
        100;

}

$conn->begin_transaction();


try {

    $sqlAnterior = "
        SELECT
            MAX(puntos) AS puntos_guardados,
            MAX(actividad_actual) AS actividad_guardada,
            MAX(porcentaje) AS porcentaje_guardado,
            MAX(completada) AS completada_guardada
        FROM capy_progreso
        WHERE userID = ?
          AND leccion_id = ?
    ";


    $stmtAnterior =
        $conn->prepare(
            $sqlAnterior
        );


    if (!$stmtAnterior) {

        throw new Exception(
            'No se pudo consultar el progreso anterior.'
        );

    }


    $stmtAnterior->bind_param(
        'ii',
        $userID,
        $leccionID
    );


    $stmtAnterior->execute();


    $resultadoAnterior =
        $stmtAnterior->get_result();


    $progresoAnterior =
        $resultadoAnterior->fetch_assoc();


    $stmtAnterior->close();


    $puntosGuardados =
        intval(
            $progresoAnterior['puntos_guardados'] ?? 0
        );


    $actividadGuardada =
        intval(
            $progresoAnterior['actividad_guardada'] ?? 0
        );


    $porcentajeGuardado =
        intval(
            $progresoAnterior['porcentaje_guardado'] ?? 0
        );


    $completadaGuardada =
        intval(
            $progresoAnterior['completada_guardada'] ?? 0
        );

    $nuevosPuntos =
        max(
            $puntosGuardados,
            $puntos
        );


    $nuevaActividad =
        max(
            $actividadGuardada,
            $actividadActual
        );


    $nuevoPorcentaje =
        max(
            $porcentajeGuardado,
            $porcentaje
        );


    $nuevaCompletada =
        max(
            $completadaGuardada,
            $completada
        );

    $puntosNuevos =
        max(
            0,
            $nuevosPuntos -
            $puntosGuardados
        );

    $sqlExiste = "
        SELECT
            COUNT(*) AS cantidad
        FROM capy_progreso
        WHERE userID = ?
          AND leccion_id = ?
    ";


    $stmtExiste =
        $conn->prepare(
            $sqlExiste
        );


    if (!$stmtExiste) {

        throw new Exception(
            'No se pudo comprobar el progreso.'
        );

    }


    $stmtExiste->bind_param(
        'ii',
        $userID,
        $leccionID
    );


    $stmtExiste->execute();


    $resultadoExiste =
        $stmtExiste->get_result();


    $filaExiste =
        $resultadoExiste->fetch_assoc();


    $stmtExiste->close();


    $existe =
        intval(
            $filaExiste['cantidad'] ?? 0
        ) > 0;


    if ($existe) {
        $sqlActualizar = "
            UPDATE capy_progreso
            SET
                actividad_actual = ?,
                porcentaje = ?,
                puntos = ?,
                completada = ?,
                ultima_actualizacion = CURRENT_TIMESTAMP
            WHERE userID = ?
              AND leccion_id = ?
        ";


        $stmtActualizar =
            $conn->prepare(
                $sqlActualizar
            );


        if (!$stmtActualizar) {

            throw new Exception(
                'No se pudo actualizar el progreso.'
            );

        }


        $stmtActualizar->bind_param(
            'iiiiii',
            $nuevaActividad,
            $nuevoPorcentaje,
            $nuevosPuntos,
            $nuevaCompletada,
            $userID,
            $leccionID
        );


        if (
            !$stmtActualizar->execute()
        ) {

            $stmtActualizar->close();

            throw new Exception(
                'No se pudo actualizar el progreso.'
            );

        }


        $stmtActualizar->close();


    } else {

        $sqlInsertar = "
            INSERT INTO capy_progreso
            (
                userID,
                leccion_id,
                actividad_actual,
                porcentaje,
                puntos,
                completada
            )
            VALUES (?, ?, ?, ?, ?, ?)
        ";


        $stmtInsertar =
            $conn->prepare(
                $sqlInsertar
            );


        if (!$stmtInsertar) {

            throw new Exception(
                'No se pudo crear el progreso.'
            );

        }


        $stmtInsertar->bind_param(
            'iiiiii',
            $userID,
            $leccionID,
            $nuevaActividad,
            $nuevoPorcentaje,
            $nuevosPuntos,
            $nuevaCompletada
        );


        if (
            !$stmtInsertar->execute()
        ) {

            $stmtInsertar->close();

            throw new Exception(
                'No se pudo guardar el progreso.'
            );

        }


        $stmtInsertar->close();

    }

    if ($puntosNuevos > 0) {


        $sqlPuntosGlobales = "
            UPDATE progreso
            SET puntos = puntos + ?
            WHERE userID = ?
        ";


        $stmtPuntosGlobales =
            $conn->prepare(
                $sqlPuntosGlobales
            );


        if (!$stmtPuntosGlobales) {

            throw new Exception(
                'No se pudo actualizar los puntos generales.'
            );

        }


        $stmtPuntosGlobales->bind_param(
            'ii',
            $puntosNuevos,
            $userID
        );


        if (
            !$stmtPuntosGlobales->execute()
        ) {

            $stmtPuntosGlobales->close();

            throw new Exception(
                'No se pudieron actualizar los puntos generales.'
            );

        }


        $stmtPuntosGlobales->close();

    }

    $conn->commit();

    responder(
        true,
        'Progreso guardado correctamente.',
        [
            'actividad_actual' =>
                $nuevaActividad,

            'porcentaje' =>
                $nuevoPorcentaje,

            'puntos' =>
                $nuevosPuntos,

            'puntos_nuevos' =>
                $puntosNuevos,

            'completada' =>
                $nuevaCompletada
        ]
    );


} catch (Throwable $error) {

    $conn->rollback();


    responder(
        false,
        'Ocurrió un error al guardar el progreso.'
    );

}

?>