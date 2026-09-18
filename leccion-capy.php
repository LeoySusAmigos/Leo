<?php

session_start();

if (!isset($_SESSION['userID'])) {
    header("Location: login.html");
    exit();
}

include 'php/conexion.php';

$userID = intval($_SESSION['userID']);


$leccionID = isset($_GET['id'])
    ? intval($_GET['id'])
    : 0;

if ($leccionID <= 0) {
    header("Location: aventura2.php");
    exit();
}


$sqlLeccion = "
    SELECT
        id,
        nivel,
        numero_leccion,
        titulo,
        descripcion,
        objetivo
    FROM capy_lecciones
    WHERE id = ?
      AND activa = 1
    LIMIT 1
";

$stmtLeccion =
    $conn->prepare($sqlLeccion);

if (!$stmtLeccion) {
    die(
        "No se pudo cargar la lección."
    );
}

$stmtLeccion->bind_param(
    'i',
    $leccionID
);

$stmtLeccion->execute();

$resultadoLeccion =
    $stmtLeccion->get_result();

$leccion =
    $resultadoLeccion->fetch_assoc();

$stmtLeccion->close();


if (!$leccion) {
    header("Location: aventura2.php");
    exit();
}

$nivelLeccion = intval($leccion['nivel']);


$desbloqueada = true;

if ($nivelLeccion > 1) {

    $nivelAnterior = $nivelLeccion - 1;

    $sqlBloqueo = "
        SELECT
            COUNT(DISTINCT l.id) AS total_lecciones,
            COUNT(
                DISTINCT CASE
                    WHEN p.completada = 1 THEN l.id
                END
            ) AS lecciones_completadas
        FROM capy_lecciones l
        LEFT JOIN capy_progreso p
            ON p.leccion_id = l.id
            AND p.userID = ?
        WHERE l.nivel = ?
          AND l.activa = 1
    ";

    $stmtBloqueo =
        $conn->prepare($sqlBloqueo);

    if (!$stmtBloqueo) {
        header("Location: aventura2.php");
        exit();
    }

    $stmtBloqueo->bind_param(
        'ii',
        $userID,
        $nivelAnterior
    );

    $stmtBloqueo->execute();

    $resultadoBloqueo =
        $stmtBloqueo->get_result();

    $filaBloqueo =
        $resultadoBloqueo->fetch_assoc();

    $totalLecciones =
        intval(
            $filaBloqueo['total_lecciones'] ?? 0
        );

    $leccionesCompletadas =
        intval(
            $filaBloqueo['lecciones_completadas'] ?? 0
        );

    $stmtBloqueo->close();
     
    if (
        $totalLecciones <= 0 ||
        $leccionesCompletadas < $totalLecciones
    ) {
        $desbloqueada = false;
    }
}

if (!$desbloqueada) {
    header("Location: aventura2.php");
    exit();
}


$progresoGuardado = [
    'actividad_actual' => 1,
    'porcentaje' => 0,
    'puntos' => 0,
    'completada' => 0
];

$sqlProgreso = "
    SELECT
        actividad_actual,
        porcentaje,
        puntos,
        completada
    FROM capy_progreso
    WHERE userID = ?
      AND leccion_id = ?
    ORDER BY id DESC
    LIMIT 1
";

$stmtProgreso =
    $conn->prepare($sqlProgreso);

if ($stmtProgreso) {

    $stmtProgreso->bind_param(
        'ii',
        $userID,
        $leccionID
    );

    $stmtProgreso->execute();

    $resultadoProgreso =
        $stmtProgreso->get_result();

    $filaProgreso =
        $resultadoProgreso->fetch_assoc();

    if ($filaProgreso) {

        $progresoGuardado =
            array_merge(
                $progresoGuardado,
                $filaProgreso
            );
    }

    $stmtProgreso->close();
}


$sqlActividades = "
    SELECT
        id,
        leccion_id,
        numero_actividad,
        tipo,
        titulo,
        instruccion,
        contenido,
        explicacion,
        audio_url,
        imagen,
        puntos,
        activa
    FROM capy_actividades
    WHERE leccion_id = ?
      AND activa = 1
    ORDER BY numero_actividad ASC
";

$stmtActividades =
    $conn->prepare($sqlActividades);

if (!$stmtActividades) {
    die(
        "No se pudieron cargar las actividades."
    );
}

$stmtActividades->bind_param(
    'i',
    $leccionID
);

$stmtActividades->execute();

$resultadoActividades =
    $stmtActividades->get_result();

$actividades = [];


while (
    $actividad =
    $resultadoActividades->fetch_assoc()
) {

    $actividadID =
        intval($actividad['id']);


    $sqlOpciones = "
        SELECT
            id,
            actividad_id,
            texto,
            imagen,
            audio_url,
            es_correcta,
            orden,
            orden_correcto,
            grupo
        FROM capy_opciones
        WHERE actividad_id = ?
        ORDER BY
            CASE
                WHEN orden_correcto IS NULL
                THEN orden
                ELSE orden_correcto
            END ASC,
            orden ASC
    ";

    $stmtOpciones =
        $conn->prepare($sqlOpciones);

    $opciones = [];

    if ($stmtOpciones) {

        $stmtOpciones->bind_param(
            'i',
            $actividadID
        );

        $stmtOpciones->execute();

        $resultadoOpciones =
            $stmtOpciones->get_result();

        while (
            $opcion =
            $resultadoOpciones->fetch_assoc()
        ) {

            $opciones[] =
                $opcion;
        }

        $stmtOpciones->close();
    }


    $actividad['opciones'] =
        $opciones;

    $actividades[] =
        $actividad;
}

$stmtActividades->close();


$totalActividades =
    count($actividades);

$actividadInicial =
    intval(
        $progresoGuardado[
            'actividad_actual'
        ] ?? 1
    );

$actividadInicial =
    max(
        1,
        min(
            $totalActividades,
            $actividadInicial
        )
    );



if (
    intval(
        $progresoGuardado['completada']
    ) === 1
) {
    $actividadInicial = 1;
}

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        <?php echo htmlspecialchars($leccion['titulo']); ?> | Capy
    </title>

    <link
        rel="stylesheet"
        href="styles/navbar.css">

    <link
        rel="stylesheet"
        href="styles/leccion-capy.css">

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

</head>

<body>

<?php include "navbar.php"; ?>

<main
    class="capy-leccion-page"

    data-leccion-id="<?php echo (int)$leccionID; ?>"

    data-actividad-inicial="<?php
        echo (int)$actividadInicial;
    ?>"

    data-puntos-iniciales="<?php
        echo (int)$progresoGuardado['puntos'];
    ?>"

    data-porcentaje-inicial="<?php
        echo (int)$progresoGuardado['porcentaje'];
    ?>"

    data-leccion-completada="<?php
        echo (int)$progresoGuardado['completada'];
    ?>">

    <!-- =====================================================
         ENCABEZADO
    ====================================================== -->

    <section class="leccion-header">

        <a
            href="aventura2.php"
            class="btn-volver">

            <i class="fa-solid fa-arrow-left"></i>

            Volver

        </a>

        <div class="leccion-header-content">

            <div class="capy-avatar-header">

                <div class="capy-avatar-circle">

                    <i class="fa-solid fa-cat"></i>

                </div>

            </div>

            <div class="leccion-header-info">

                <span class="nivel-label">

                    Nivel
                    <?php echo htmlspecialchars(
                        $leccion['nivel']
                    ); ?>

                </span>

                <h1>

                    <?php echo htmlspecialchars(
                        $leccion['titulo']
                    ); ?>

                </h1>

                <?php if (!empty($leccion['descripcion'])): ?>

                    <p>

                        <?php echo htmlspecialchars(
                            $leccion['descripcion']
                        ); ?>

                    </p>

                <?php endif; ?>

            </div>

        </div>

        <!-- =================================================
             PROGRESO
        ================================================== -->

        <div class="leccion-progress">

            <div class="progress-info">

                <span>
                    Progreso de la lección
                </span>

                <strong>

                    <span id="actividadActual">
                        <?php echo $actividadInicial; ?>
                    </span>

                    /

                    <?php echo $totalActividades; ?>

                </strong>

            </div>

            <div class="progress-bar">

                <div
                    class="progress-fill"
                    id="progressFill"
                    style="width:
                    <?php
                    echo $progresoGuardado['porcentaje'];
                    ?>%;">

                </div>

            </div>

        </div>

    </section>

    <!-- =====================================================
         CONTENIDO DE LA LECCIÓN
    ====================================================== -->

    <section class="leccion-content">

    <?php foreach (
        $actividades as $indice => $actividad
    ): ?>

        <?php

        $tipo = strtolower(
            trim($actividad['tipo'])
        );

        echo '<!-- TIPO ACTIVIDAD: ' . htmlspecialchars($tipo) . ' -->';

        $numeroActividad = $indice + 1;

        ?>

        <article

            class="actividad-card
            <?php
            echo $indice === 0
                ? 'actividad-activa'
                : '';
            ?>"

            data-actividad="<?php
                echo $numeroActividad;
            ?>"

            data-actividad-id="<?php
                echo (int)$actividad['id'];
            ?>"

            data-leccion-id="<?php
                echo (int)$leccionID;
            ?>"

            data-tipo="<?php
                echo htmlspecialchars($tipo);
            ?>"

            data-puntos="<?php
                echo (int)$actividad['puntos'];
            ?>">

            <!-- =================================================
                 PARTE SUPERIOR
            ================================================== -->

            <div class="actividad-top">

                <span class="actividad-numero">

                    Actividad
                    <?php echo $numeroActividad; ?>

                </span>

                <span class="actividad-puntos">

                    <?php echo (int)$actividad['puntos']; ?>

                    pts

                </span>

            </div>

            <!-- =================================================
                 ICONO
            ================================================== -->

            <div class="actividad-icon">

                <?php

                $icono = "fa-star";

                switch ($tipo) {

                    case 'introduccion':
                        $icono = "fa-lightbulb";
                        break;

                    case 'explicacion':
                        $icono = "fa-book-open";
                        break;

                    case 'clasificacion':
                        $icono = "fa-layer-group";
                        break;

                    case 'seleccion':
                        $icono = "fa-circle-check";
                        break;

                    case 'ordenar':
                    case 'orden':
                        $icono = "fa-list-ol";
                        break;

                    case 'conectar':
                        $icono = "fa-link";
                        break;

                    case 'reto':
                    case 'arrastrar_articulo':
                        $icono = "fa-puzzle-piece";
                        break;
                }

                ?>

                <i class="fa-solid <?php echo $icono; ?>"></i>

            </div>

            <!-- =================================================
                 TÍTULO
            ================================================== -->

            <h2>

                <?php echo htmlspecialchars(
                    $actividad['titulo']
                ); ?>

            </h2>

            <!-- =================================================
                 INSTRUCCIÓN
            ================================================== -->

            <?php if (
                !empty($actividad['instruccion'])
            ): ?>

                <div class="actividad-instruccion">

                    <i class="fa-solid fa-circle-info"></i>

                    <span>

                        <?php echo htmlspecialchars(
                            $actividad['instruccion']
                        ); ?>

                    </span>

                </div>

            <?php endif; ?>

            <!-- =================================================
                 INTRODUCCIÓN / EXPLICACIÓN
            ================================================== -->

            <?php if (
                $tipo === 'introduccion' ||
                $tipo === 'explicacion'
            ): ?>

                <?php if (
                    !empty($actividad['contenido'])
                ): ?>

                    <div class="actividad-contenido">

                        <p>

                            <?php echo nl2br(
                                htmlspecialchars(
                                    $actividad['contenido']
                                )
                            ); ?>

                        </p>

                    </div>

                <?php endif; ?>

                <?php if (
                    !empty($actividad['imagen'])
                ): ?>

                    <div class="actividad-imagen">

                        <img
                            src="<?php echo htmlspecialchars(
                                $actividad['imagen']
                            ); ?>"

                            alt="<?php echo htmlspecialchars(
                                $actividad['titulo']
                            ); ?>">

                    </div>

                <?php endif; ?>

                <?php if (
                    !empty($actividad['audio_url'])
                ): ?>

                    <button
                        type="button"
                        class="btn-audio"

                        data-audio="<?php echo htmlspecialchars(
                            $actividad['audio_url']
                        ); ?>">

                        <i class="fa-solid fa-volume-high"></i>

                        Escuchar

                    </button>

                <?php endif; ?>

            <!-- =================================================
                ARRASTRE DE SUSTANTIVOS
            ================================================== -->

            <?php elseif ($tipo === 'arrastre'): ?>

                <?php

                $opcionesArrastre = $actividad['opciones'] ?? [];
                $usarOpciones = !empty($opcionesArrastre);

                if (!$usarOpciones) {
                    $palabras = array_filter(
                        array_map(
                            'trim',
                            explode('|', $actividad['contenido'] ?? '')
                        )
                    );
                }
                ?>

                <div class="juego-arrastrar-sustantivos">

                    <!-- INSTRUCCIÓN DEL JUEGO -->
                    <div class="instruccion-sustantivos">

                        <i class="fa-solid fa-hand-pointer"></i>

                        <div>

                            <strong>¡Encuentra los sustantivos!</strong>

                            <span>
                                Arrastra todos los sustantivos hasta el espacio de la derecha.
                            </span>

                        </div>

                    </div>


                    <!-- ÁREA DEL JUEGO -->
                    <div class="sustantivos-juego">


                        <!-- PALABRAS DISPONIBLES -->
                        <div class="sustantivos-disponibles">

                            <div class="sustantivos-titulo">

                                <i class="fa-solid fa-font"></i>

                                <span>Palabras</span>

                            </div>


                            <div class="sustantivos-lista">

                                <?php if ($usarOpciones): ?>

                                    <?php foreach ($opcionesArrastre as $opcion): ?>

                                        <button
                                            type="button"
                                            class="sustantivo-arrastrable"
                                            draggable="true"
                                            data-sustantivo-id="<?php
                                                echo (int)($opcion['id'] ?? 0);
                                            ?>"
                                            data-texto="<?php
                                                echo htmlspecialchars(
                                                    trim($opcion['texto'] ?? ''),
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                );
                                            ?>"
                                            data-correcta="<?php
                                                echo (int)($opcion['es_correcta'] ?? 0);
                                            ?>"
                                        >

                                            <i class="fa-solid fa-tag"></i>

                                            <span>
                                                <?php
                                                echo htmlspecialchars(
                                                    trim($opcion['texto'] ?? ''),
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                );
                                                ?>
                                            </span>

                                        </button>

                                    <?php endforeach; ?>

                                <?php else: ?>

                                    <?php foreach ($palabras as $indiceSustantivo => $sustantivo): ?>

                                        <button
                                            type="button"
                                            class="sustantivo-arrastrable"
                                            draggable="true"
                                            data-sustantivo-id="<?php
                                                echo 'contenido-' . $indiceSustantivo;
                                            ?>"
                                            data-texto="<?php
                                                echo htmlspecialchars(
                                                    trim($sustantivo),
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                );
                                            ?>"
                                            data-correcta="1"
                                        >

                                            <i class="fa-solid fa-tag"></i>

                                            <span>
                                                <?php
                                                echo htmlspecialchars(
                                                    trim($sustantivo),
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                );
                                                ?>
                                            </span>

                                        </button>

                                    <?php endforeach; ?>

                                <?php endif; ?>

                            </div>

                        </div>


                        <!-- ZONA DE DESTINO -->
                        <div
                            class="zona-sustantivos"
                            data-placeholder="Arrastra aquí los sustantivos..."
                        >

                            <div class="zona-sustantivos-titulo">

                                <i class="fa-solid fa-box-open"></i>

                                <span>Sustantivos</span>

                            </div>


                            <div class="zona-sustantivos-contenido">

                                <div class="zona-sustantivos-placeholder">

                                    <i class="fa-solid fa-arrow-right"></i>

                                    <span>
                                        Arrastra aquí los sustantivos
                                    </span>

                                </div>

                            </div>

                        </div>

                    </div>


                    <!-- MENSAJE DE RESULTADO -->
                    <div
                        class="resultado-sustantivos"
                        hidden
                    >

                        <i class="fa-solid fa-circle-check"></i>

                        <span>
                            ¡Excelente! Reconociste todos los sustantivos.
                        </span>

                    </div>

                </div>

            <!-- =================================================
                ORDENAR
            ================================================== -->

            <?php elseif (
                (
                    $tipo === 'ordenar' ||
                    $tipo === 'orden'
                ) &&
                (int)($actividad['leccion_id'] ?? 0) === 1 &&
                (int)($actividad['numero_actividad'] ?? 0) === 4
            ): ?>

                <!-- =========================================
                    ACTIVIDAD 4 - LECCIÓN 1
                    RECONOCER EL SUSTANTIVO
                ========================================== -->

                <div class="ordenar-juego">

                    <div class="ordenar-mensaje">

                        <div class="ordenar-mensaje-icon">

                            <i class="fa-solid fa-magnifying-glass"></i>

                        </div>

                        <div>

                            <strong>
                                ¡Encuentra el sustantivo de animal!
                            </strong>

                        </div>

                    </div>


                    <div class="palabras-orden">

                        <?php foreach (
                            $actividad['opciones']
                            as $opcion
                        ): ?>

                            <button
                                type="button"

                                class="palabra-orden"

                                draggable="false"

                                data-opcion-id="<?php
                                    echo (int)$opcion['id'];
                                ?>"

                                data-orden=""

                                data-correcta="<?php
                                    echo (int)($opcion['es_correcta'] ?? 0);
                                ?>"

                                data-grupo="<?php
                                    echo htmlspecialchars(
                                        $opcion['grupo'] ?? '',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                ?>"

                                data-audio="<?php
                                    echo htmlspecialchars(
                                        $opcion['audio_url'] ?? '',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                ?>">

                                <?php if (
                                    !empty($opcion['imagen'])
                                ): ?>

                                    <img
                                        src="<?php
                                            echo htmlspecialchars(
                                                $opcion['imagen'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );
                                        ?>"

                                        alt="<?php
                                            echo htmlspecialchars(
                                                $opcion['texto'] ?? '',
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );
                                        ?>">

                                <?php endif; ?>


                                <span>

                                    <?php
                                    echo htmlspecialchars(
                                        $opcion['texto'] ?? '',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>

                                </span>

                            </button>

                        <?php endforeach; ?>

                    </div>


                    <!-- =========================================
                        RETROALIMENTACIÓN
                    ========================================== -->

                    <div
                        class="actividad-feedback"
                        hidden
                    ></div>

                </div>


            <?php elseif (
                $tipo === 'ordenar' ||
                $tipo === 'orden'
            ): ?>

                <!-- =========================================
                    ORDENAR NORMAL
                    Todas las demás actividades
                ========================================== -->

                <div class="ordenar-juego">

                    <div class="ordenar-mensaje">

                        <div class="ordenar-mensaje-icon">

                            <i class="fa-solid fa-magnifying-glass"></i>

                        </div>

                        <div>

                            <strong>
                                ¡Encuentra el sustantivo de animal!
                            </strong>

                        </div>

                    </div>


                    <div class="oracion-resultado">

                        <div class="resultado-icon">

                            <i class="fa-solid fa-comment"></i>

                        </div>

                        <div
                            class="zona-oracion"
                            data-placeholder="Coloca aquí las palabras...">

                        </div>

                    </div>


                    <div class="palabras-orden">

                        <?php foreach (
                            $actividad['opciones']
                            as $opcion
                        ): ?>

                            <button
                                type="button"

                                class="palabra-orden"

                                draggable="true"

                                data-opcion-id="<?php
                                    echo (int)$opcion['id'];
                                ?>"

                                data-orden="<?php
                                    echo $opcion['orden_correcto'] !== null
                                        ? (int)$opcion['orden_correcto']
                                        : '';
                                ?>"

                                data-correcta="<?php
                                    echo (int)($opcion['es_correcta'] ?? 0);
                                ?>"

                                data-grupo="<?php
                                    echo htmlspecialchars(
                                        $opcion['grupo'] ?? '',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                ?>"

                                data-audio="<?php
                                    echo htmlspecialchars(
                                        $opcion['audio_url'] ?? '',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                ?>">

                                <?php if (
                                    !empty($opcion['imagen'])
                                ): ?>

                                    <img
                                        src="<?php
                                            echo htmlspecialchars(
                                                $opcion['imagen'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );
                                        ?>"

                                        alt="<?php
                                            echo htmlspecialchars(
                                                $opcion['texto'] ?? '',
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );
                                        ?>">

                                <?php endif; ?>


                                <span>

                                    <?php
                                    echo htmlspecialchars(
                                        $opcion['texto'] ?? '',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>

                                </span>

                            </button>

                        <?php endforeach; ?>

                    </div>

                </div>

            <!-- =================================================
                 CONECTAR
            ================================================== -->

            <?php elseif ($tipo === 'conectar'): ?>

                <div class="conectar-juego">

                    <div class="conectar-instruccion">

                        <i class="fa-solid fa-link"></i>

                        <span>
                            Une cada palabra con la imagen que corresponde.
                        </span>

                    </div>


                    <div class="conectar-columnas">

                        <!-- =========================================
                            PALABRAS
                        ========================================== -->

                        <div class="conectar-columna conectar-columna-palabras">

                            <h3>
                                <i class="fa-solid fa-font"></i>
                                Palabras
                            </h3>

                            <div class="conectar-palabras">

                                <?php foreach (
                                    $actividad['opciones']
                                    as $opcion
                                ): ?>

                                    <button
                                        type="button"
                                        class="elemento-conectar palabra-conectar"

                                        data-pareja="<?php
                                            echo (int)$opcion['id'];
                                        ?>"

                                        data-grupo="<?php
                                            echo htmlspecialchars(
                                                $opcion['grupo'] ?? '',
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );
                                        ?>"

                                        data-texto="<?php
                                            echo htmlspecialchars(
                                                $opcion['texto'] ?? '',
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );
                                        ?>"
                                    >

                                        <span class="palabra-conectar-texto">

                                            <?php echo htmlspecialchars(
                                                $opcion['texto'] ?? '',
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ); ?>

                                        </span>

                                    </button>

                                <?php endforeach; ?>

                            </div>

                        </div>


                        <!-- =========================================
                            IMÁGENES
                        ========================================== -->

                        <div class="conectar-columna conectar-columna-imagenes">

                            <h3>
                                <i class="fa-regular fa-image"></i>
                                Imágenes
                            </h3>

                            <div class="conectar-imagenes">

                                <?php foreach (
                                    $actividad['opciones']
                                    as $opcion
                                ): ?>

                                    <?php if (
                                        !empty($opcion['imagen'])
                                    ): ?>

                                        <button
                                            type="button"
                                            class="elemento-conectar imagen-conectar"

                                            data-pareja="<?php
                                                echo (int)$opcion['id'];
                                            ?>"

                                            data-grupo="<?php
                                                echo htmlspecialchars(
                                                    $opcion['grupo'] ?? '',
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                );
                                            ?>"

                                            data-texto="<?php
                                                echo htmlspecialchars(
                                                    $opcion['texto'] ?? '',
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                );
                                            ?>"
                                        >

                                            <img
                                                src="<?php echo htmlspecialchars(
                                                    $opcion['imagen'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ); ?>"

                                                alt="<?php echo htmlspecialchars(
                                                    $opcion['texto'] ?? '',
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ); ?>"
                                            >

                                        </button>

                                    <?php endif; ?>

                                <?php endforeach; ?>

                            </div>

                        </div>


                    </div>

                    <!-- =========================================
                        CONEXIONES REALIZADAS
                    ========================================== -->

                    <div class="conexiones-realizadas">

                        <span class="conexion-contador">
                            <i class="fa-solid fa-link"></i>
                            Parejas conectadas:
                            <strong class="conexiones-numero">0</strong>
                            /
                            <strong>
                                <?php echo count($actividad['opciones']); ?>
                            </strong>
                        </span>

                    </div>

                </div>



            <!-- =================================================
                 CLASIFICACIÓN
            ================================================== -->

            <?php elseif (
                $tipo === 'clasificacion'
            ): ?>

                <div class="clasificacion-juego">

                    <div class="clasificacion-palabras">

                        <?php foreach (
                            $actividad['opciones']
                            as $opcion
                        ): ?>

                            <button
                                type="button"

                                class="palabra-arrastrable"

                                draggable="true"

                                data-opcion-id="<?php
                                    echo (int)$opcion['id'];
                                ?>"

                                data-grupo="<?php
                                    echo htmlspecialchars(
                                        $opcion['grupo'] ?? ''
                                    );
                                ?>"

                                data-audio="<?php
                                    echo htmlspecialchars(
                                        $opcion['audio_url'] ?? ''
                                    );
                                ?>">

                                <?php if (
                                    !empty($opcion['imagen'])
                                ): ?>

                                    <img
                                        src="<?php echo htmlspecialchars(
                                            $opcion['imagen']
                                        ); ?>"

                                        alt="<?php echo htmlspecialchars(
                                            $opcion['texto'] ?? ''
                                        ); ?>">

                                <?php endif; ?>

                                <?php if (
                                    !empty($opcion['texto'])
                                ): ?>

                                    <span>

                                        <?php echo htmlspecialchars(
                                            $opcion['texto']
                                        ); ?>

                                    </span>

                                <?php endif; ?>

                                <?php if (
                                    !empty($opcion['audio_url'])
                                ): ?>

                                    <span
                                        class="opcion-audio"

                                        data-audio="<?php
                                            echo htmlspecialchars(
                                                $opcion['audio_url']
                                            );
                                        ?>">

                                        <i class="fa-solid fa-volume-high"></i>

                                    </span>

                                <?php endif; ?>

                            </button>

                        <?php endforeach; ?>

                    </div>

                    <div class="grupos-destino">

                        <?php

                        $grupos = [

                            'persona' => [
                                'Persona',
                                '¿Quién?',
                                'fa-user'
                            ],

                            'animal' => [
                                'Animal',
                                '¿Qué animal?',
                                'fa-paw'
                            ],

                            'lugar' => [
                                'Lugar',
                                '¿Dónde?',
                                'fa-location-dot'
                            ],

                            'objeto' => [
                                'Objeto',
                                '¿Qué?',
                                'fa-cube'
                            ]

                        ];

                        ?>

                        <?php foreach (
                            $grupos as $clave => $grupo
                        ): ?>

                            <div

                                class="grupo-destino
                                       grupo-<?php echo $clave; ?>"

                                data-grupo="<?php
                                    echo $clave;
                                ?>">

                                <div class="grupo-destino-header">

                                    <div class="grupo-destino-icon">

                                        <i class="fa-solid
                                            <?php echo $grupo[2]; ?>">
                                        </i>

                                    </div>

                                    <div>

                                        <strong>
                                            <?php echo $grupo[0]; ?>
                                        </strong>

                                        <span>
                                            <?php echo $grupo[1]; ?>
                                        </span>

                                    </div>

                                </div>

                                <div class="grupo-palabras"></div>

                            </div>

                        <?php endforeach; ?>

                    </div>

                </div>

            <!-- =================================================
                 SELECCIÓN / RETO
            ================================================== -->

            <?php else: ?>

                <?php if (
                    !empty($actividad['contenido'])
                ): ?>

                    <div class="actividad-contenido">

                        <?php

                        $contenido =
                            $actividad['contenido'];

                        if (
                            strpos(
                                $contenido,
                                '|'
                            ) !== false
                        ):

                            $elementos =
                                explode(
                                    '|',
                                    $contenido
                                );

                        ?>

                            <div class="contenido-palabras">

                                <?php foreach (
                                    $elementos
                                    as $elemento
                                ): ?>

                                    <div class="palabra-item">

                                        <?php echo htmlspecialchars(
                                            trim($elemento)
                                        ); ?>

                                    </div>

                                <?php endforeach; ?>

                            </div>

                        <?php else: ?>

                            <p>

                                <?php echo nl2br(
                                    htmlspecialchars(
                                        $contenido
                                    )
                                ); ?>

                            </p>

                        <?php endif; ?>

                    </div>

                <?php endif; ?>

                <?php if (
                    !empty($actividad['audio_url'])
                ): ?>

                    <button
                        type="button"
                        class="btn-audio"

                        data-audio="<?php
                            echo htmlspecialchars(
                                $actividad['audio_url']
                            );
                        ?>">

                        <i class="fa-solid fa-volume-high"></i>

                        Escuchar

                    </button>

                <?php endif; ?>

                <?php if (
                    !empty($actividad['imagen'])
                ): ?>

                    <div class="actividad-imagen">

                        <img
                            src="<?php
                                echo htmlspecialchars(
                                    $actividad['imagen']
                                );
                            ?>"

                            alt="<?php
                                echo htmlspecialchars(
                                    $actividad['titulo']
                                );
                            ?>">

                    </div>

                <?php endif; ?>

                <?php if (
                    !empty($actividad['opciones'])
                ): ?>

                    <div
                        class="opciones-container tipo-<?php
                            echo htmlspecialchars(
                                $tipo
                            );
                        ?>">

                        <?php foreach (
                            $actividad['opciones']
                            as $opcion
                        ): ?>

                            <button
                                type="button"

                                class="opcion-capy"

                                data-opcion-id="<?php
                                    echo (int)$opcion['id'];
                                ?>"

                                data-correcta="<?php
                                    echo (int)$opcion['es_correcta'];
                                ?>"

                                data-grupo="<?php
                                    echo htmlspecialchars(
                                        $opcion['grupo'] ?? ''
                                    );
                                ?>"

                                data-orden-correcto="<?php
                                    echo $opcion['orden_correcto'] !== null
                                        ? (int)$opcion['orden_correcto']
                                        : '';
                                ?>"

                                data-audio="<?php
                                    echo htmlspecialchars(
                                        $opcion['audio_url'] ?? ''
                                    );
                                ?>">

                                <?php if (
                                    !empty($opcion['imagen'])
                                ): ?>

                                    <img
                                        src="<?php
                                            echo htmlspecialchars(
                                                $opcion['imagen']
                                            );
                                        ?>"

                                        alt="<?php
                                            echo htmlspecialchars(
                                                $opcion['texto'] ?? ''
                                            );
                                        ?>">

                                <?php endif; ?>

                                <?php if (
                                    !empty($opcion['texto'])
                                ): ?>

                                    <span>

                                        <?php echo htmlspecialchars(
                                            $opcion['texto']
                                        ); ?>

                                    </span>

                                <?php endif; ?>

                                <?php if (
                                    !empty($opcion['audio_url'])
                                ): ?>

                                    <span
                                        class="opcion-audio"

                                        data-audio="<?php
                                            echo htmlspecialchars(
                                                $opcion['audio_url']
                                            );
                                        ?>">

                                        <i class="fa-solid fa-volume-high"></i>

                                    </span>

                                <?php endif; ?>

                            </button>

                        <?php endforeach; ?>

                    </div>

                <?php endif; ?>

            <?php endif; ?>

            <!-- =================================================
                EXPLICACIÓN DE CAPY
            ================================================== -->

            <?php
            $tipoActividad = strtolower(
                trim($actividad['tipo'] ?? '')
            );

            $esExplicacion =
                $tipoActividad === 'introduccion' ||
                $tipoActividad === 'explicacion';
            ?>

            <?php if (!empty($actividad['explicacion'])): ?>

                <div class="actividad-explicacion">

                    <div class="capy-mini">

                        <i class="fa-solid fa-cat"></i>

                    </div>

                    <div>

                        <strong>
                            Capy dice:
                        </strong>

                        <p>

                            <?php echo nl2br(
                                htmlspecialchars(
                                    $actividad['explicacion']
                                )
                            ); ?>

                        </p>

                    </div>

                </div>

            <?php endif; ?>


            <?php if (
                $esExplicacion &&
                !empty($actividad['explicacion'])
            ): ?>

                <div class="actividad-boton-entendido">

                    <button
                        type="button"
                        class="btn-actividad btn-completar">

                        <i class="fa-solid fa-check"></i>

                        ¡Entendido!

                    </button>

                </div>

            <?php endif; ?>


            <!-- =================================================
                FEEDBACK
            ================================================== -->

            <div
                class="actividad-feedback"
                id="feedback-<?php
                    echo (int)$actividad['id'];
                ?>">
            </div>


            <!-- =================================================
                BOTONES DE NAVEGACIÓN
            ================================================== -->

            <div class="actividad-actions">

                <?php if ($indice > 0): ?>

                    <button
                        type="button"
                        class="btn-actividad btn-anterior">

                        <i class="fa-solid fa-arrow-left"></i>

                        Anterior

                    </button>

                <?php endif; ?>


                <?php if (
                    $indice <
                    $totalActividades - 1
                ): ?>

                    <button
                        type="button"
                        class="btn-actividad btn-siguiente">

                        Siguiente

                        <i class="fa-solid fa-arrow-right"></i>

                    </button>

                <?php else: ?>

                    <a
                        href="aventura2.php"
                        type="button"
                        class="btn-actividad btn-finalizar">

                        <i class="fa-solid fa-star"></i>

                        Finalizar lección

                    </a>

                <?php endif; ?>

            </div>

        </article>

    <?php endforeach; ?>

    </section>

</main>

<script src="js/leccion-capy.js"></script>

</body>

</html>