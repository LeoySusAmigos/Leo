<?php
session_start();

if (!isset($_SESSION['userID'])) {
    header("Location: register.html");
    exit();
}

include("php/conexion.php");

$nombre      = $_SESSION['nombre_nino'];
$avatar_nino = $_SESSION['foto_nino'];
$idUsuario   = $_SESSION['userID'];

/* ============================================================
   AJUSTES RÁPIDOS
   ============================================================ */

/** Segundos mínimos para contar un libro como leído. */
define('SEGUNDOS_LECTURA_VALIDA', 15);

/** Bajo este porcentaje, una lección aparece como "necesita apoyo". */
define('UMBRAL_APOYO', 60);

/* ============================================================
   HELPERS
   ============================================================ */

/** Evita que la página truene si una tabla aún no existe (Capy). */
function tablaExiste($conn, $tabla)
{
    $r = $conn->query("SHOW TABLES LIKE '" . $conn->real_escape_string($tabla) . "'");
    return $r && $r->num_rows > 0;
}

/**
 * Devuelve id => nombre desde una tabla de catálogo.
 * Si no existe, devuelve [] y se muestra un nombre genérico.
 */
function mapaNombres($conn, $tabla, $colId, $colNombre)
{
    if (!tablaExiste($conn, $tabla)) return [];
    $res = @$conn->query("SELECT `$colId` AS id, `$colNombre` AS nombre FROM `$tabla`");
    if (!$res) return [];
    $mapa = [];
    while ($f = $res->fetch_assoc()) $mapa[$f['id']] = $f['nombre'];
    return $mapa;
}

function nombreDe($mapa, $id, $prefijo)
{
    return isset($mapa[$id]) ? $mapa[$id] : $prefijo . ' ' . $id;
}

function tiempoLegible($segundos)
{
    return $segundos < 60 ? $segundos . ' s' : round($segundos / 60) . ' min';
}

/** Cuenta actividades por semana; la posición 3 es la semana actual. */
function serieSemanal($fechas)
{
    $serie = [0, 0, 0, 0];
    $inicioActual = strtotime('monday this week');
    foreach ($fechas as $f) {
        $t = strtotime($f);
        for ($i = 0; $i < 4; $i++) {
            $desde = strtotime('-' . (3 - $i) . ' week', $inicioActual);
            $hasta = strtotime('+7 day', $desde);
            if ($t >= $desde && $t < $hasta) $serie[$i]++;
        }
    }
    return $serie;
}

/* ============================================================
   PROGRESO GENERAL
   ============================================================ */

$stmt = $conn->prepare("SELECT * FROM progreso WHERE userID = ?");
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$datos = $stmt->get_result()->fetch_assoc();

if (!$datos) {
    $datos = [
        'nivel_actual'   => 0,
        'porcentaje'     => 0,
        'leccion_actual' => 1,
        'puntos'         => 0,
        'racha'          => 0
    ];
}

/* Catálogos opcionales: si no existen, se usan nombres genéricos */
$nombresLecciones = mapaNombres($conn, 'lecciones', 'leccionID', 'nombre');
$nombresLibros    = mapaNombres($conn, 'libros', 'libro_id', 'titulo');

/* ============================================================
   LEO — letras, sílabas y palabras
   ============================================================ */

$leoLecciones = [];
if (tablaExiste($conn, 'leo_progreso')) {
    $stmt = $conn->prepare("SELECT nivelID, leccionID,
                                   MAX(porcentaje) AS porcentaje,
                                   MAX(ultima_actualizacion) AS fecha
                            FROM leo_progreso
                            WHERE userID = ?
                            GROUP BY nivelID, leccionID
                            ORDER BY nivelID, leccionID");
    $stmt->bind_param("i", $idUsuario);
    $stmt->execute();
    $leoLecciones = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

$leoCompletadas = array_values(array_filter($leoLecciones, fn($l) => $l['porcentaje'] >= 100));
$leoEnProceso   = array_values(array_filter($leoLecciones, fn($l) => $l['porcentaje'] < 100));
$leoPorcentaje  = count($leoLecciones)
    ? (int) round(array_sum(array_column($leoLecciones, 'porcentaje')) / count($leoLecciones))
    : 0;

/* ============================================================
   FINX — historias y lectura
   ============================================================ */

$finxLibros = [];
if (tablaExiste($conn, 'progreso_libros')) {
    $stmt = $conn->prepare("SELECT libro_id,
                                   COUNT(*) AS veces,
                                   SUM(tiempo_segundos) AS tiempo,
                                   MAX(fecha_leido) AS fecha
                            FROM progreso_libros
                            WHERE userID = ?
                            GROUP BY libro_id
                            ORDER BY fecha DESC");
    $stmt->bind_param("i", $idUsuario);
    $stmt->execute();
    $finxLibros = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

$librosLeidos      = array_values(array_filter($finxLibros, fn($l) => $l['tiempo'] >= SEGUNDOS_LECTURA_VALIDA));
$librosAbandonados = array_values(array_filter($finxLibros, fn($l) => $l['tiempo'] < SEGUNDOS_LECTURA_VALIDA));
$finxPorcentaje    = count($finxLibros) ? (int) round(count($librosLeidos) / count($finxLibros) * 100) : 0;

/* ============================================================
   CAPY — pendiente de crear la tabla `capy_progreso`
   Columnas esperadas: userID, porcentaje, fecha (o similar).
   Cuando exista, esta sección se activa sola.
   ============================================================ */

$capyDisponible  = tablaExiste($conn, 'capy_progreso');
$capyActividades = 0;
$capyPorcentaje  = 0;

if ($capyDisponible) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS total, AVG(porcentaje) AS promedio
                            FROM capy_progreso WHERE userID = ?");
    $stmt->bind_param("i", $idUsuario);
    $stmt->execute();
    $r = $stmt->get_result()->fetch_assoc();
    $capyActividades = (int) $r['total'];
    $capyPorcentaje  = (int) round($r['promedio']);
}

/* ============================================================
   FECHAS DE ACTIVIDAD (gráfica, semana y racha)
   ============================================================ */

$fechasLeo    = [];
$fechasLibros = [];

if (tablaExiste($conn, 'leo_progreso')) {
    $stmt = $conn->prepare("SELECT DATE(ultima_actualizacion) AS d FROM leo_progreso WHERE userID = ?");
    $stmt->bind_param("i", $idUsuario);
    $stmt->execute();
    foreach ($stmt->get_result()->fetch_all(MYSQLI_ASSOC) as $f) $fechasLeo[] = $f['d'];
}

if (tablaExiste($conn, 'progreso_libros')) {
    $stmt = $conn->prepare("SELECT DATE(fecha_leido) AS d FROM progreso_libros WHERE userID = ?");
    $stmt->bind_param("i", $idUsuario);
    $stmt->execute();
    foreach ($stmt->get_result()->fetch_all(MYSQLI_ASSOC) as $f) $fechasLibros[] = $f['d'];
}

$serieLecciones = serieSemanal($fechasLeo);
$serieLibros    = serieSemanal($fechasLibros);
$serieTotal     = [];
for ($i = 0; $i < 4; $i++) $serieTotal[$i] = $serieLecciones[$i] + $serieLibros[$i];

$series = [
    'actividades' => $serieTotal,
    'lecciones'   => $serieLecciones,
    'libros'      => $serieLibros,
];

$todasLasFechas = array_unique(array_merge($fechasLeo, $fechasLibros));
rsort($todasLasFechas);

$inicioSemana = date('Y-m-d', strtotime('monday this week'));
$diasActivos  = array_values(array_filter($todasLasFechas, fn($d) => $d >= $inicioSemana));

/* Racha real: días seguidos con actividad, contando desde hoy o ayer */
$racha = 0;
if (count($todasLasFechas)) {
    $cursor = in_array(date('Y-m-d'), $todasLasFechas)
        ? date('Y-m-d')
        : date('Y-m-d', strtotime('-1 day'));

    while (in_array($cursor, $todasLasFechas)) {
        $racha++;
        $cursor = date('Y-m-d', strtotime($cursor . ' -1 day'));
    }
}

/* ============================================================
   ÚLTIMOS APRENDIZAJES
   ============================================================ */

$aprendizajes = [];

foreach ($leoLecciones as $l) {
    $aprendizajes[] = [
        'titulo'     => nombreDe($nombresLecciones, $l['leccionID'], 'Lección'),
        'mascota'    => 'Leo',
        'clase'      => 'leo',
        'img'        => 'images/Leito.png',
        'fecha'      => $l['fecha'],
        'completado' => $l['porcentaje'] >= 100,
    ];
}

foreach ($finxLibros as $l) {
    $aprendizajes[] = [
        'titulo'     => nombreDe($nombresLibros, $l['libro_id'], 'Libro'),
        'mascota'    => 'Finx',
        'clase'      => 'finx',
        'img'        => 'images/finxito3.png',
        'fecha'      => $l['fecha'],
        'completado' => $l['tiempo'] >= SEGUNDOS_LECTURA_VALIDA,
    ];
}

usort($aprendizajes, fn($a, $b) => strtotime($b['fecha']) - strtotime($a['fecha']));
$ultimosAprendizajes = array_slice($aprendizajes, 0, 5);

/* ============================================================
   DÓNDE NECESITA MÁS APOYO
   ============================================================ */

$areasApoyo = [];

foreach ($leoEnProceso as $l) {
    if ($l['porcentaje'] < UMBRAL_APOYO) {
        $areasApoyo[] = [
            'titulo'  => nombreDe($nombresLecciones, $l['leccionID'], 'Lección'),
            'mascota' => 'Leo',
            'clase'   => 'leo',
            'dato'    => $l['porcentaje'] . '% completado',
            'consejo' => 'Repitan esta lección juntos antes de avanzar.',
        ];
    }
}

foreach ($librosAbandonados as $l) {
    $areasApoyo[] = [
        'titulo'  => nombreDe($nombresLibros, $l['libro_id'], 'Libro'),
        'mascota' => 'Finx',
        'clase'   => 'finx',
        'dato'    => 'Solo ' . tiempoLegible($l['tiempo']) . ' de lectura',
        'consejo' => 'Acompáñalo en este cuento para que no lo deje a medias.',
    ];
}

if (!$capyDisponible || $capyActividades === 0) {
    $areasApoyo[] = [
        'titulo'  => 'Gramática y oraciones',
        'mascota' => 'Capy',
        'clase'   => 'capy',
        'dato'    => 'Sin actividades registradas',
        'consejo' => 'Todavía no practica con Capy. Es un buen paso siguiente.',
    ];
}

/* ============================================================
   SIGUE CON…
   ============================================================ */

if (count($leoEnProceso) > 0 || count($leoLecciones) === 0) {
    $siguiente = ['nombre' => 'Leo',  'clase' => 'leo',  'img' => 'images/Leito.png',   'pct' => $leoPorcentaje,  'enlace' => 'aventura-leo.php'];
} elseif (count($librosLeidos) < 3) {
    $siguiente = ['nombre' => 'Finx', 'clase' => 'finx', 'img' => 'images/finxito3.png',  'pct' => $finxPorcentaje, 'enlace' => 'biblioteca.php'];
} else {
    $siguiente = ['nombre' => 'Capy', 'clase' => 'capy', 'img' => 'images/capy1.png', 'pct' => $capyPorcentaje, 'enlace' => 'aventura2.php'];
}

/* ============================================================
   RANKING DE MASCOTAS
   ============================================================ */

$usoMascotas = [
    ['nombre' => 'Leo',  'clase' => 'leo',  'img' => 'images/Leito.png',   'total' => count($leoLecciones)],
    ['nombre' => 'Finx', 'clase' => 'finx', 'img' => 'images/finxito3.png',  'total' => count($finxLibros)],
    ['nombre' => 'Capy', 'clase' => 'capy', 'img' => 'images/capy1.png', 'total' => $capyActividades],
];
usort($usoMascotas, fn($a, $b) => $b['total'] - $a['total']);
$maxUso = max(1, $usoMascotas[0]['total']);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Progreso de <?php echo htmlspecialchars($nombre); ?></title>
    <link rel="shortcut icon" href="images/favicon/favicon-32x32.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@500;600;700&family=Quicksand:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/navbar1.css">
    <link rel="stylesheet" href="styles/progreso.css">
</head>

<body>

    <?php include("navbar1.php"); ?>

    <main class="lienzo">
        <div class="contenido">

            <!-- ====== Perfil + progreso ====== -->
            <section class="rejilla rejilla-perfil">

                <article class="panel panel-perfil">
                    <header class="panel-titulo">
                        <span class="icono-titulo chip-verde"><i class="fa-solid fa-square-check"></i></span>
                        <h2>Mi perfil</h2>
                    </header>

                    <div class="perfil-cuerpo">
                        <div class="avatar">
                            <?php if (!empty($avatar_nino)): ?>
                                <?php echo $avatar_nino; ?>
                            <?php else: ?>
                                <i class="fa-solid fa-user"></i>
                            <?php endif; ?>
                        </div>

                        <div class="perfil-texto">
                            <h1>Progreso de <?php echo htmlspecialchars($nombre); ?></h1>
                            <p class="perfil-meta">
                                <strong>Nivel <?php echo $datos['nivel_actual']; ?></strong>
                                <span>Sigue aprendiendo y ganando logros</span>
                            </p>
                        </div>

                        <img src="images/Leito.png" alt="Leo" class="perfil-mascota" aria-hidden="true">
                    </div>
                </article>

                <article class="panel">
                    <header class="panel-titulo">
                        <span class="icono-titulo chip-verde"><i class="fa-solid fa-bolt"></i></span>
                        <h2>Mi progreso</h2>
                    </header>

                    <p class="nivel-texto">
                        Nivel <?php echo $datos['nivel_actual']; ?> — <?php echo $datos['porcentaje']; ?>% completado
                    </p>

                    <div class="barra-fila">
                        <span class="barra-fondo">
                            <span class="barra-relleno" data-ancho="<?php echo (int) $datos['porcentaje']; ?>"></span>
                        </span>
                        <span class="barra-valor"><?php echo $datos['porcentaje']; ?>%</span>
                    </div>

                    <div class="metricas">
                        <div class="metrica">
                            <span class="icono-metrica chip-amarillo"><i class="fa-solid fa-star"></i></span>
                            <div>
                                <p class="metrica-numero" data-contador="<?php echo (int) $datos['puntos']; ?>">0</p>
                                <span>Puntos</span>
                            </div>
                        </div>
                        <div class="metrica">
                            <span class="icono-metrica chip-azul"><i class="fa-solid fa-book"></i></span>
                            <div>
                                <p class="metrica-numero" data-contador="<?php echo count($leoCompletadas); ?>">0</p>
                                <span>Lecciones completadas</span>
                            </div>
                        </div>
                        <div class="metrica">
                            <span class="icono-metrica chip-morado"><i class="fa-solid fa-book-open"></i></span>
                            <div>
                                <p class="metrica-numero" data-contador="<?php echo count($librosLeidos); ?>">0</p>
                                <span>Libros completados</span>
                            </div>
                        </div>
                    </div>
                </article>

            </section>

            <!-- ====== Gráfica + sigue con ====== -->
            <section class="rejilla rejilla-grafica">

                <article class="panel">
                    <header class="panel-titulo panel-titulo-flex">
                        <div class="titulo-izq">
                            <span class="icono-titulo chip-verde"><i class="fa-solid fa-chart-simple"></i></span>
                            <div>
                                <h2>Evolución del aprendizaje</h2>
                                <p class="panel-sub">Actividades completadas durante las últimas 4 semanas.</p>
                            </div>
                        </div>

                        <label class="selector">
                            <span class="oculto">Qué mostrar en la gráfica</span>
                            <select id="serieSelect">
                                <option value="actividades">Actividades</option>
                                <option value="lecciones">Lecciones</option>
                                <option value="libros">Libros</option>
                            </select>
                        </label>
                    </header>

                    <div class="grafica-caja">
                        <svg id="grafica" viewBox="0 0 560 260" role="img"
                            aria-label="Actividades completadas por semana durante las últimas cuatro semanas">
                            <defs>
                                <linearGradient id="degradadoArea" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="#2fa84f" stop-opacity="0.22" />
                                    <stop offset="100%" stop-color="#2fa84f" stop-opacity="0" />
                                </linearGradient>
                            </defs>
                            <g id="ejes"></g>
                            <path id="area" fill="url(#degradadoArea)"></path>
                            <polyline id="linea" fill="none" stroke="#2fa84f" stroke-width="3"
                                stroke-linecap="round" stroke-linejoin="round"></polyline>
                            <g id="puntos"></g>
                        </svg>
                    </div>
                </article>

                <article class="panel panel-sigue sigue-<?php echo $siguiente['clase']; ?>">
                    <img src="<?php echo $siguiente['img']; ?>" alt="<?php echo $siguiente['nombre']; ?>" class="sigue-mascota">
                    <div>
                        <h2>Sigue con <?php echo $siguiente['nombre']; ?></h2>
                        <p class="sigue-meta">
                            Nivel <?php echo $datos['nivel_actual']; ?> · <?php echo $siguiente['pct']; ?>% completado
                        </p>
                        <a href="<?php echo $siguiente['enlace']; ?>" class="boton boton-<?php echo $siguiente['clase']; ?>">
                            Continuar aprendiendo <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </article>

            </section>

            <!-- ====== Progreso por personaje ====== -->
            <section class="panel">
                <header class="panel-titulo">
                    <span class="icono-titulo chip-verde"><i class="fa-solid fa-users"></i></span>
                    <h2>Tu progreso con cada personaje</h2>
                </header>

                <div class="personajes">

                    <article class="personaje personaje-leo">
                        <div class="personaje-cabecera">
                            <img src="images/Leito.png" alt="Leo">
                            <div>
                                <h3>Leo</h3>
                                <p>Letras, sílabas y palabras</p>
                            </div>
                        </div>
                        <div class="barra-fila">
                            <span class="barra-fondo">
                                <span class="barra-relleno relleno-leo" data-ancho="<?php echo $leoPorcentaje; ?>"></span>
                            </span>
                            <span class="barra-valor valor-leo"><?php echo $leoPorcentaje; ?>%</span>
                        </div>
                        <p class="personaje-pie">
                            <?php echo count($leoCompletadas); ?> lecciones ·
                            <?php echo count($leoEnProceso); ?> en proceso
                        </p>
                    </article>

                    <article class="personaje personaje-capy <?php echo $capyDisponible ? '' : 'sin-datos'; ?>">
                        <div class="personaje-cabecera">
                            <img src="images/capy1.png" alt="Capy">
                            <div>
                                <h3>Capy</h3>
                                <p>Gramática y oraciones</p>
                            </div>
                        </div>
                        <div class="barra-fila">
                            <span class="barra-fondo">
                                <span class="barra-relleno relleno-capy" data-ancho="<?php echo $capyPorcentaje; ?>"></span>
                            </span>
                            <span class="barra-valor valor-capy"><?php echo $capyPorcentaje; ?>%</span>
                        </div>
                        <p class="personaje-pie">
                            <?php echo $capyDisponible
                                ? $capyActividades . ' actividades completadas'
                                : 'Disponible pronto'; ?>
                        </p>
                    </article>

                    <article class="personaje personaje-finx">
                        <div class="personaje-cabecera">
                            <img src="images/finxito3.png" alt="Finx">
                            <div>
                                <h3>Finx</h3>
                                <p>Historias y lectura</p>
                            </div>
                        </div>
                        <div class="barra-fila">
                            <span class="barra-fondo">
                                <span class="barra-relleno relleno-finx" data-ancho="<?php echo $finxPorcentaje; ?>"></span>
                            </span>
                            <span class="barra-valor valor-finx"><?php echo $finxPorcentaje; ?>%</span>
                        </div>
                        <p class="personaje-pie">
                            <?php echo count($librosLeidos); ?> libros ·
                            <?php echo count($librosAbandonados); ?> sin terminar
                        </p>
                    </article>

                </div>
            </section>

            <!-- ====== Ranking + semana ====== -->
            <section class="rejilla rejilla-dos">

                <article class="panel">
                    <header class="panel-titulo">
                        <span class="icono-titulo chip-verde"><i class="fa-solid fa-users"></i></span>
                        <h2>¿Con quién ha practicado más?</h2>
                    </header>

                    <div class="ranking">
                        <?php foreach ($usoMascotas as $m): ?>
                            <div class="fila-ranking">
                                <img src="<?php echo $m['img']; ?>" alt="<?php echo $m['nombre']; ?>" class="ranking-avatar">
                                <span class="ranking-nombre nombre-<?php echo $m['clase']; ?>"><?php echo $m['nombre']; ?></span>
                                <span class="barra-fondo">
                                    <span class="barra-relleno relleno-<?php echo $m['clase']; ?>"
                                        data-ancho="<?php echo round($m['total'] / $maxUso * 100); ?>"></span>
                                </span>
                                <span class="ranking-total"><?php echo $m['total']; ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <p class="nota-pie">Lecciones, actividades y libros completados.</p>
                </article>

                <article class="panel">
                    <header class="panel-titulo">
                        <span class="icono-titulo chip-verde"><i class="fa-solid fa-calendar-check"></i></span>
                        <h2>Días de práctica esta semana</h2>
                    </header>

                    <div class="semana">
                        <?php
                        $etiquetas = ['L', 'M', 'X', 'J', 'V', 'S', 'D'];
                        foreach ($etiquetas as $i => $d):
                            $fechaDia = date('Y-m-d', strtotime($inicioSemana . " +$i day"));
                            $activo   = in_array($fechaDia, $diasActivos);
                        ?>
                            <div class="dia">
                                <span class="dia-letra"><?php echo $d; ?></span>
                                <span class="dia-marca <?php echo $activo ? 'activo' : ''; ?>"
                                    title="<?php echo $fechaDia; ?>">
                                    <i class="fa-solid <?php echo $activo ? 'fa-check' : 'fa-minus'; ?>"></i>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <p class="nota-pie">
                        <i class="fa-solid fa-fire"></i>
                        <?php echo $racha > 0
                            ? "$racha día(s) seguidos practicando. ¡Sigan así!"
                            : "Practiquen hoy para empezar una racha."; ?>
                    </p>
                </article>

            </section>

            <!-- ====== Dónde necesita más apoyo ====== -->
            <section class="panel">
                <header class="panel-titulo">
                    <span class="icono-titulo chip-naranja"><i class="fa-solid fa-hand-holding-heart"></i></span>
                    <div>
                        <h2>Dónde necesita más apoyo</h2>
                        <p class="panel-sub">Temas donde le cuesta avanzar y qué pueden hacer juntos.</p>
                    </div>
                </header>

                <?php if (count($areasApoyo) === 0): ?>
                    <div class="vacio">
                        <img src="images/capy1.png" alt="Capy">
                        <div>
                            <h4>Todo va bien por ahora</h4>
                            <p><?php echo htmlspecialchars($nombre); ?> no tiene temas atrasados. Sigan practicando así.</p>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="apoyo">
                        <?php foreach ($areasApoyo as $a): ?>
                            <article class="tarjeta-apoyo">
                                <div class="apoyo-cabecera">
                                    <h4><?php echo htmlspecialchars($a['titulo']); ?></h4>
                                    <span class="etiqueta etiqueta-<?php echo $a['clase']; ?>"><?php echo $a['mascota']; ?></span>
                                </div>
                                <p class="apoyo-dato"><?php echo htmlspecialchars($a['dato']); ?></p>
                                <p class="apoyo-consejo"><?php echo htmlspecialchars($a['consejo']); ?></p>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>

            <!-- ====== Últimos aprendizajes ====== -->
            <section class="panel">
                <header class="panel-titulo panel-titulo-flex">
                    <div class="titulo-izq">
                        <span class="icono-titulo chip-verde"><i class="fa-solid fa-book-open"></i></span>
                        <h2>Últimos aprendizajes</h2>
                    </div>
                    <a href="#todos" class="boton-suave">
                        Ver todos los aprendizajes <i class="fa-solid fa-chevron-right"></i>
                    </a>
                </header>

                <?php if (count($ultimosAprendizajes) === 0): ?>
                    <p class="nota-pie">Aquí verás lo último que <?php echo htmlspecialchars($nombre); ?> ha practicado.</p>
                <?php else: ?>
                    <ul class="aprendizajes">
                        <?php foreach ($ultimosAprendizajes as $a): ?>
                            <li>
                                <span class="marca <?php echo $a['completado'] ? 'marca-ok' : 'marca-proceso'; ?>">
                                    <i class="fa-solid <?php echo $a['completado'] ? 'fa-check' : 'fa-hourglass-half'; ?>"></i>
                                </span>
                                <span class="aprendizaje-titulo"><?php echo htmlspecialchars($a['titulo']); ?></span>
                                <span class="aprendizaje-fecha"><?php echo date('d M', strtotime($a['fecha'])); ?></span>
                                <span class="aprendizaje-mascota">
                                    <img src="<?php echo $a['img']; ?>" alt="<?php echo $a['mascota']; ?>" class="aprendizaje-avatar">
                                    <span class="nombre-<?php echo $a['clase']; ?>"><?php echo $a['mascota']; ?></span>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </section>

            <!-- ====== Lecciones completadas ====== -->
            <section class="panel" id="todos">
                <header class="panel-titulo">
                    <span class="icono-titulo chip-verde"><i class="fa-solid fa-trophy"></i></span>
                    <div>
                        <h2>Lecciones completadas</h2>
                        <p class="panel-sub">Todo lo que <?php echo htmlspecialchars($nombre); ?> ya terminó.</p>
                    </div>
                </header>

                <?php if (count($leoCompletadas) === 0 && count($librosLeidos) === 0 && count($leoEnProceso) === 0): ?>
                    <div class="vacio">
                        <img src="images/Leito.png" alt="Leito">
                        <div>
                            <h4>Todavía no hay lecciones completadas</h4>
                            <p>Cuando termine su primera lección, aparecerá aquí.</p>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (count($leoCompletadas)): ?>
                    <h3 class="subtitulo">Con Leo</h3>
                    <ul class="aprendizajes">
                        <?php foreach ($leoCompletadas as $l): ?>
                            <li>
                                <span class="marca marca-ok"><i class="fa-solid fa-check"></i></span>
                                <span class="aprendizaje-titulo">
                                    <?php echo htmlspecialchars(nombreDe($nombresLecciones, $l['leccionID'], 'Lección')); ?>
                                </span>
                                <span class="aprendizaje-fecha">Nivel <?php echo $l['nivelID']; ?></span>
                                <span class="aprendizaje-fecha"><?php echo date('d M', strtotime($l['fecha'])); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <?php if (count($librosLeidos)): ?>
                    <h3 class="subtitulo">Con Finx</h3>
                    <ul class="aprendizajes">
                        <?php foreach ($librosLeidos as $l): ?>
                            <li>
                                <span class="marca marca-ok"><i class="fa-solid fa-check"></i></span>
                                <span class="aprendizaje-titulo">
                                    <?php echo htmlspecialchars(nombreDe($nombresLibros, $l['libro_id'], 'Libro')); ?>
                                </span>
                                <span class="aprendizaje-fecha"><?php echo tiempoLegible($l['tiempo']); ?></span>
                                <span class="aprendizaje-fecha"><?php echo date('d M', strtotime($l['fecha'])); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <?php if (count($leoEnProceso)): ?>
                    <h3 class="subtitulo">En proceso</h3>
                    <ul class="aprendizajes">
                        <?php foreach ($leoEnProceso as $l): ?>
                            <li>
                                <span class="marca marca-proceso"><i class="fa-solid fa-hourglass-half"></i></span>
                                <span class="aprendizaje-titulo">
                                    <?php echo htmlspecialchars(nombreDe($nombresLecciones, $l['leccionID'], 'Lección')); ?>
                                </span>
                                <span class="mini-barra-fondo">
                                    <span class="mini-barra" style="width: <?php echo (int) $l['porcentaje']; ?>%"></span>
                                </span>
                                <span class="aprendizaje-fecha"><?php echo (int) $l['porcentaje']; ?>%</span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </section>

            <!-- ====== Cierre ====== -->
            <footer class="cierre">
                <p>¡Cada pequeño paso<br>te acerca a grandes logros!</p>
                <div class="cierre-mascotas">
                    <img src="images/Leito.png" alt="Leito">
                    <img src="images/capy1.png" alt="Capy">
                    <img src="images/finxito3.png" alt="Finx">
                </div>
            </footer>

        </div>
    </main>

    <!-- Datos para la gráfica -->
    <script type="application/json" id="datos-grafica">
        <?php echo json_encode($series); ?>
    </script>

    <script src="js/progreso.js"></script>

</body>

</html>