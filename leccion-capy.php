<?php
session_start();

require_once "php/conexion.php";

if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: aventura2.php");
    exit();
}

$leccion_id = intval($_GET['id']);
$userID = intval($_SESSION['userID']);

/* =========================================================
   LECCIÓN
========================================================= */

$sqlLeccion = "SELECT id, nivel, numero_leccion, titulo, descripcion, objetivo
               FROM capy_lecciones
               WHERE id = ? AND activa = 1
               LIMIT 1";

$stmtLeccion = $conn->prepare($sqlLeccion);

if (!$stmtLeccion) {
    header("Location: aventura2.php");
    exit();
}

$stmtLeccion->bind_param("i", $leccion_id);
$stmtLeccion->execute();

$resultLeccion = $stmtLeccion->get_result();

if ($resultLeccion->num_rows === 0) {
    $stmtLeccion->close();
    header("Location: aventura2.php");
    exit();
}

$leccion = $resultLeccion->fetch_assoc();

$stmtLeccion->close();

/* =========================================================
   PROGRESO GUARDADO
========================================================= */

$progresoGuardado = [
    'actividad_actual' => 1,
    'porcentaje' => 0,
    'puntos' => 0,
    'completada' => 0
];

$sqlProgreso = "SELECT actividad_actual, porcentaje, puntos, completada
                FROM capy_progreso
                WHERE userID = ? AND leccion_id = ?
                LIMIT 1";

$stmtProgreso = $conn->prepare($sqlProgreso);

if ($stmtProgreso) {

    $stmtProgreso->bind_param(
        "ii",
        $userID,
        $leccion_id
    );

    $stmtProgreso->execute();

    $resultProgreso = $stmtProgreso->get_result();

    if ($resultProgreso->num_rows > 0) {
        $datosProgreso = $resultProgreso->fetch_assoc();

        $progresoGuardado = [
            'actividad_actual' => max(
                1,
                intval($datosProgreso['actividad_actual'])
            ),
            'porcentaje' => max(
                0,
                min(
                    100,
                    intval($datosProgreso['porcentaje'])
                )
            ),
            'puntos' => max(
                0,
                intval($datosProgreso['puntos'])
            ),
            'completada' => intval(
                $datosProgreso['completada']
            )
        ];
    }

    $stmtProgreso->close();
}

/* =========================================================
   ACTIVIDADES
========================================================= */

$sqlActividades = "SELECT id, leccion_id, numero_actividad, tipo,
                          titulo, instruccion, contenido,
                          explicacion, audio_url, imagen,
                          puntos, activa
                   FROM capy_actividades
                   WHERE leccion_id = ? AND activa = 1
                   ORDER BY numero_actividad ASC";

$stmtActividades = $conn->prepare($sqlActividades);

if (!$stmtActividades) {
    header("Location: aventura2.php");
    exit();
}

$stmtActividades->bind_param(
    "i",
    $leccion_id
);

$stmtActividades->execute();

$resultActividades = $stmtActividades->get_result();

$actividades = [];

while ($actividad = $resultActividades->fetch_assoc()) {

    $actividad_id = intval($actividad['id']);

    /* =====================================================
       OPCIONES
    ===================================================== */

    $sqlOpciones = "SELECT id, actividad_id, texto, imagen,
                           audio_url, es_correcta, orden,
                           orden_correcto, grupo
                    FROM capy_opciones
                    WHERE actividad_id = ?
                    ORDER BY
                        CASE
                            WHEN orden_correcto IS NULL
                            THEN orden
                            ELSE orden_correcto
                        END ASC,
                        orden ASC";

    $stmtOpciones = $conn->prepare($sqlOpciones);

    $opciones = [];

    if ($stmtOpciones) {

        $stmtOpciones->bind_param(
            "i",
            $actividad_id
        );

        $stmtOpciones->execute();

        $resultOpciones = $stmtOpciones->get_result();

        while ($opcion = $resultOpciones->fetch_assoc()) {
            $opciones[] = $opcion;
        }

        $stmtOpciones->close();
    }

    $actividad['opciones'] = $opciones;

    $actividades[] = $actividad;
}

$stmtActividades->close();

$totalActividades = count($actividades);

if ($totalActividades === 0) {
    header("Location: aventura2.php");
    exit();
}

/*
 * La actividad guardada no puede superar la cantidad
 * de actividades existentes.
 */
$actividadInicial = min(
    max(
        1,
        $progresoGuardado['actividad_actual']
    ),
    $totalActividades
);

/*
 * Si la lección ya está completada, comenzamos desde
 * la primera actividad para permitir repasarla.
 */
if ($progresoGuardado['completada'] == 1) {
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

    data-leccion-id="<?php
        echo (int)$leccion['id'];
    ?>"

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
                echo (int)$leccion_id;
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
                 ARRASTRAR ARTÍCULO
            ================================================== -->
            <?php elseif ($tipo === 'arrastrar_articulo' || $tipo === 'arrastre'): ?>

                <div class="juego-arrastrar-articulo">
                    <div class="instruccion-arrastre">
                        <i class="fa-solid fa-hand-pointer"></i>
                        <span>Arrastra el artículo correcto hasta el espacio vacío.</span>
                    </div>

                    <div class="frase-arrastre">
                        <?php
                        $frase = htmlspecialchars(
                            $actividad['contenido'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        if (strpos($frase, '___') === false) {
                            $frase .= ' ___';
                        }
                        $frase = str_replace(
                            '___',
                            '<span class="espacio-articulo" data-articulo="">?</span>',
                            $frase
                        );
                        echo $frase;
                        ?>
                    </div>

                    <div class="articulos-arrastrables">
                        <?php foreach ($actividad['opciones'] as $opcion): ?>
                            <button
                                type="button"
                                class="articulo-arrastrable"
                                draggable="true"
                                data-opcion-id="<?php echo (int)$opcion['id']; ?>"
                                data-correcta="<?php echo (int)$opcion['es_correcta']; ?>"
                                data-texto="<?php echo htmlspecialchars($opcion['texto'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                data-audio="<?php echo htmlspecialchars($opcion['audio_url'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                            >
                                <span class="articulo-icono">
                                    <i class="fa-solid fa-puzzle-piece"></i>
                                </span>
                                <span><?php echo htmlspecialchars($opcion['texto'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                            </button>
                        <?php endforeach; ?>
                    </div>

                    <button
                        type="button"
                        class="btn-reiniciar-arrastre"
                        hidden
                    >
                        <i class="fa-solid fa-rotate-right"></i>
                        Intentar de nuevo
                    </button>
                </div>

            <!-- =================================================
                 ORDENAR
            ================================================== -->

            <?php elseif (
                $tipo === 'ordenar' ||
                $tipo === 'orden'
            ): ?>

                <div class="ordenar-juego">

                    <div class="ordenar-mensaje">

                        <div class="ordenar-mensaje-icon">

                            <i class="fa-solid fa-wand-magic-sparkles"></i>

                        </div>

                        <div>

                            <strong>
                                Construye la oración
                            </strong>

                            <span>
                                Coloca las palabras en el orden correcto.
                            </span>

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

                                <span>

                                    <?php echo htmlspecialchars(
                                        $opcion['texto'] ?? ''
                                    ); ?>

                                </span>

                            </button>

                        <?php endforeach; ?>

                    </div>

                    <button
                        type="button"
                        class="btn-reiniciar-juego">

                        <i class="fa-solid fa-rotate"></i>

                        Reiniciar

                    </button>

                </div>

            <!-- =================================================
                 CONECTAR
            ================================================== -->

            <?php elseif (
                $tipo === 'conectar'
            ): ?>

                <?php

                $opcionesTexto = [];
                $opcionesImagen = [];

                foreach (
                    $actividad['opciones']
                    as $opcion
                ) {

                    if (
                        empty($opcion['imagen']) &&
                        !empty($opcion['texto'])
                    ) {

                        $opcionesTexto[] = $opcion;

                    } elseif (
                        !empty($opcion['imagen'])
                    ) {

                        $opcionesImagen[] = $opcion;
                    }
                }

                ?>

                <div class="conectar-juego">

                    <div class="conectar-instruccion">

                        <div class="conectar-instruccion-icon">

                            <i class="fa-solid fa-link"></i>

                        </div>

                        <div>

                            <strong>
                                Une cada palabra con su imagen
                            </strong>

                            <span>
                                Primero toca una palabra y luego
                                toca la imagen que corresponde.
                            </span>

                        </div>

                    </div>

                    <div class="conectar-columnas">

                        <!-- PALABRAS -->

                        <div class="conectar-columna">

                            <h3>

                                <i class="fa-solid fa-font"></i>

                                Palabras

                            </h3>

                            <div class="conectar-palabras">

                                <?php foreach (
                                    $opcionesTexto
                                    as $opcion
                                ): ?>

                                    <button
                                        type="button"

                                        class="elemento-conectar
                                               palabra-conectar"

                                        data-id="<?php
                                            echo (int)$opcion['id'];
                                        ?>"

                                        data-grupo="<?php
                                            echo htmlspecialchars(
                                                $opcion['grupo'] ?? ''
                                            );
                                        ?>">

                                        <span>

                                            <?php echo htmlspecialchars(
                                                $opcion['texto']
                                            ); ?>

                                        </span>

                                    </button>

                                <?php endforeach; ?>

                            </div>

                        </div>

                        <!-- IMÁGENES -->

                        <div class="conectar-columna">

                            <h3>

                                <i class="fa-solid fa-image"></i>

                                Imágenes

                            </h3>

                            <div class="conectar-imagenes">

                                <?php foreach (
                                    $opcionesImagen
                                    as $opcion
                                ): ?>

                                    <button
                                        type="button"

                                        class="elemento-conectar
                                               imagen-conectar"

                                        data-id="<?php
                                            echo (int)$opcion['id'];
                                        ?>"

                                        data-grupo="<?php
                                            echo htmlspecialchars(
                                                $opcion['grupo'] ?? ''
                                            );
                                        ?>">

                                        <img
                                            src="<?php echo htmlspecialchars(
                                                $opcion['imagen']
                                            ); ?>"

                                            alt="<?php echo htmlspecialchars(
                                                $opcion['texto']
                                                ?? 'Imagen'
                                            ); ?>">

                                    </button>

                                <?php endforeach; ?>

                            </div>

                        </div>

                    </div>

                    <svg
                        class="conectar-lineas"
                        aria-hidden="true">
                    </svg>

                    <div class="conexiones-realizadas"></div>

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

            <?php if (
                !empty($actividad['explicacion'])
            ): ?>

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

                    <a href="aventura2.php" type="button" class="btn-actividad btn-finalizar">
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