<?php
session_start();
include("php/conexion.php");

if (!isset($_SESSION['userID'])) {
    header("Location: login.html");
    exit();
}

$userID = $_SESSION['userID'];


/* =========================
   DATOS DEL USUARIO
========================= */

$sql     = "SELECT * FROM usuarios WHERE userID='$userID'";
$usuario = $conn->query($sql)->fetch_assoc();

$fotoNino = !empty($usuario['foto_nino'])
    ? $usuario['foto_nino']
    : 'default.png';


/* =========================
   PROGRESO GENERAL
========================= */

$sql       = "SELECT * FROM progreso WHERE userID='$userID'";
$resultado = $conn->query($sql);

if ($resultado->num_rows == 0) {
    $conn->query("INSERT INTO progreso(userID) VALUES('$userID')");
    $resultado = $conn->query("SELECT * FROM progreso WHERE userID='$userID'");
}

$progreso = $resultado->fetch_assoc();


/* =========================
   VARIABLES GENERALES
========================= */

$puntos        = $progreso['puntos'];
$racha         = $progreso['racha'];
$nivel         = $progreso['nivel_actual'];
$leccionActual = $progreso['leccion_actual'];

// Porcentaje de Leo (lectura)
$porcentajeLeo = min(100, max(0, $progreso['porcentaje'] ?? 0));

// Porcentaje de Capy (gramática)
// OJO: ajusta 'porcentaje_capy' al nombre real de tu columna en la tabla progreso
$porcentajeCapy = min(100, max(0, $progreso['porcentaje_capy'] ?? 0));


/* =========================
   MÓDULO ACTUAL
========================= */

$moduloActual = $progreso['modulo_actual'] ?? 'leo';


/* =========================
   DESBLOQUEOS DE AVENTURAS
========================= */

// Si el usuario tiene rol "admin", las 3 mascotas quedan desbloqueadas
$esAdmin = isset($usuario['rol']) && $usuario['rol'] === 'admin';

// Capy se desbloquea al completar Leo al 100% (o si es admin)
$capyDesbloqueado = $esAdmin || $porcentajeLeo >= 0;

// Finx se desbloquea al completar Capy al 100% (o si es admin)
$finxDesbloqueado = $esAdmin || ($capyDesbloqueado && $porcentajeCapy >= 0);


/* =========================
   CUENTOS LEÍDOS
========================= */

$sql    = "SELECT COUNT(*) total FROM progreso_libros WHERE userID='$userID'";
$libros = $conn->query($sql)->fetch_assoc();
$cuentosLeidos = $libros['total'];


/* =========================
   ÚLTIMO LOGRO
========================= */

$sql = "
    SELECT l.titulo, l.descripcion
    FROM usuario_logros ul
    JOIN logros l ON ul.logroID = l.logroID
    WHERE ul.userID = '$userID'
    ORDER BY ul.fecha DESC
    LIMIT 1
";

$mision = $conn->query($sql);

if ($mision && $mision->num_rows > 0) {
    $mision_completed = $mision->fetch_assoc();
} else {
    $mision_completed = [
        'titulo'      => 'Esperando tu primera misión',
        'descripcion' => 'Completa misiones para obtener tu primer logro'
    ];
}

/* =========================
   TEXTO DEL MÓDULO ACTUAL
   (misma lógica que antes, solo movida a variable para reutilizarla en el nuevo layout)
========================= */

if ($moduloActual == 'leo') {
    $moduloTexto = "Sílabas con Leo";
} elseif ($moduloActual == 'capy') {
    $moduloTexto = "Gramática con Capy";
} else {
    $moduloTexto = "Biblioteca con Finx";
}

$porcentajeGeneral = 0;
if ($moduloActual == 'leo') {
    $porcentajeGeneral = $porcentajeLeo;
} elseif ($moduloActual == 'capy') {
    $porcentajeGeneral = $porcentajeCapy;
} else {
    $porcentajeGeneral = 0;
}

/* =========================
   URL DEL BOTÓN "CONTINUAR MI AVENTURA"
   (usa la misma variable $moduloActual que ya existía)
========================= */

if ($moduloActual == 'leo') {
    $urlContinuar = 'aventura-leo.php';
} elseif ($moduloActual == 'capy') {
    $urlContinuar = 'aventura2.php';
} else {
    $urlContinuar = 'biblioteca.php';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio Niño</title>
    <link rel="shortcut icon" href="images/favicon/favicon-32x32.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Balsamiq+Sans:wght@700&family=Fredoka:wght@600;900&family=Nunito:wght@700;900&family=Quicksand:wght@500;700;900&display=swap" rel="stylesheet">    <link rel="stylesheet" href="styles/navbar.css">
    <link rel="stylesheet" href="styles/inicio-nino.css">
</head>
<body>

    <?php include 'navbar.php'; ?>

    <!-- ══════════════════════════════
         HERO DASHBOARD
    ══════════════════════════════ -->
    <section class="hero-dashboard">

        <div class="hero-mascota">
            <img src="images/leito.png" alt="Leo">
        </div>

        <div class="hero-info">

            <h1>¡Hola, <?= htmlspecialchars($usuario['nombre_nino']) ?>!</h1>

            <p class="hero-mensaje">
                ¡Qué bueno verte de nuevo! Sigue aprendiendo, jugando y descubriendo cosas increíbles.
            </p>

            <p class="hero-modulo">
                Sigues en <strong><?= $moduloTexto ?></strong> — Nivel <?= $nivel ?>
            </p>

            <div class="barra-progreso">
                <div class="barra-interna" id="barraHero" data-progreso="<?= $porcentajeGeneral ?>" style="width:0%"></div>
            </div>

            <span class="barra-texto"><?= $porcentajeGeneral ?>% completado</span>

            <a href="<?= $urlContinuar ?>" class="btn-continuar" <?= $moduloActual == 'leo' ? "onclick=\"sessionStorage.setItem('desdeMenuLeo','true')\"" : "" ?>>
                Continuar mi aventura
                <i class="fa-solid fa-arrow-right"></i>
            </a>

        </div>

    </section>


    <!-- ══════════════════════════════
         AVENTURAS
    ══════════════════════════════ -->
    <section class="aventuras">

        <h2>
            <span class="hoja">
                <i class="fa-solid fa-seedling" style="color: rgb(56, 177, 0);"></i>
            </span> 
            Elige tu aventura 
            <span class="hoja">
                <i class="fa-solid fa-seedling" style="color: rgb(56, 177, 0);"></i>
            </span>
        </h2>

        <div class="aventuras-grid">

            <!-- Leo -->
            <div class="aventura leo-card">
                <div class="aventura-img-wrap">
                    <img src="images/Leito.png" alt="Leo">
                </div>
                <div class="aventura-body">
                    <h3>Leo</h3>
                    <p class="aventura-tema">Letras, sílabas y palabras</p>

                    <?php if ($porcentajeLeo > 0): ?>
                        <div class="mini-barra">
                            <div class="mini-barra-interna" data-progreso="<?= $porcentajeLeo ?>" style="width:0%"></div>
                        </div>
                        <span class="mini-porcentaje"><?= $porcentajeLeo ?>% completado</span>
                    <?php endif; ?>

                    <a href="aventura-leo.php" class="btn-aventura" onclick="sessionStorage.setItem('desdeMenuLeo','true')">
                        ¡Aprender! <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- Capy -->
            <div class="aventura capy-card">
                <div class="aventura-img-wrap">
                    <img src="images/capy1.png" alt="Capy">
                </div>
                <div class="aventura-body">
                    <h3>Capy</h3>
                    <p class="aventura-tema">Gramática y oraciones</p>

                    <?php if ($capyDesbloqueado): ?>
                        <div class="mini-barra">
                            <div class="mini-barra-interna" data-progreso="<?= $porcentajeCapy ?>" style="width:0%"></div>
                        </div>
                        <?php if ($porcentajeCapy > 0): ?>
                            <span class="mini-porcentaje"><?= $porcentajeCapy ?>% completado</span>
                        <?php endif; ?>
                        <a href="aventura2.php" class="btn-aventura">
                            ¡Aprender! <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    <?php else: ?>
                        <div class="bloqueado"><i class="fa-solid fa-lock" style="color: rgb(124, 124, 124);"></i> Completa Leo</div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Finx -->
            <div class="aventura finx-card">
                <div class="aventura-img-wrap">
                    <img src="images/finxito3.png" alt="Finx">
                </div>
                <div class="aventura-body">
                    <h3>Finx</h3>
                    <p class="aventura-tema">Historias y lectura</p>

                    <?php if ($finxDesbloqueado): ?>
                        <p class="finx-cuentos"><?= $cuentosLeidos ?> cuentos leídos</p>
                        <a href="biblioteca.php" class="btn-aventura">
                            ¡Aprender! <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    <?php else: ?>
                        <div class="bloqueado"><i class="fa-solid fa-lock" style="color: rgb(124, 124, 124);"></i> Completa Capy</div>
                    <?php endif; ?>
                </div>
            </div>

        </div>

    </section>


    <!-- ══════════════════════════════
         ESTADÍSTICAS
    ══════════════════════════════ -->
    <section class="estadisticas-nino">

        <div class="hoja-esquina hoja-izq" aria-hidden="true">
            <i class="fa-brands fa-pagelines" style="color: rgb(56, 177, 0);"></i>
        </div>

        <div class="card-estadistica">
            <span class="stat-icono"><i class="fa-solid fa-star" style="color: rgb(255, 212, 59);"></i></span>
            <h3><?= $puntos ?></h3>
            <p>Puntos</p>
        </div>

        <div class="stat-divisor"></div>

        <div class="card-estadistica">
            <span class="stat-icono"><i class="fa-solid fa-fire" style="color: rgb(255, 153, 0);"></i></span>
            <h3><?= $racha ?></h3>
            <p>Días seguidos</p>
        </div>

        <div class="stat-divisor"></div>

        <div class="card-estadistica">
            <span class="stat-icono"><i class="fa-solid fa-book-bookmark" style="color: rgb(59, 167, 255);"></i></span>
            <h3><?= $cuentosLeidos ?></h3>
            <p>Aventuras leídas</p>
        </div>

        <div class="hoja-esquina hoja-der" aria-hidden="true">
            <i class="fa-brands fa-pagelines" style="color: rgb(57, 183, 0);"></i>
        </div>

    </section>


    <!-- ══════════════════════════════
         MOTIVACIÓN + ÚLTIMO LOGRO
    ══════════════════════════════ -->
    <section class="motivacion">

        <div class="motivacion-texto">
            <div class="logro-icono">
                <i class="fa-solid fa-gift" style="color: #19d800;"></i>
            </div>
            <div>
                <h3>¡Lo estás haciendo genial!</h3>
                <p class="motivacion-frase">¡Vamos y completemos esas misiones, Explorador!</p>
                <div class="ultimo-logro">
                    <strong><?= htmlspecialchars($mision_completed['titulo']) ?></strong>
                    <span><?= htmlspecialchars($mision_completed['descripcion']) ?></span>
                </div>
            </div>
        </div>

        <div class="motivacion-personajes">
            <img src="images/Leito.png" alt="Leo">
            <img src="images/capy1.png" alt="Capy">
            <img src="images/finxito3.png" alt="Finx">
        </div>

    </section>


    <!-- ══════════════════════════════
         FOOTER
    ══════════════════════════════ -->
    <section class="dashboard-footer">
        <h2>¡Sigue aprendiendo!</h2>
        <p>Cada aventura te ayuda a crecer.</p>
    </section>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/inicio-nino.js"></script>
    <script src="js/navbar.js"></script>

</body>
</html>