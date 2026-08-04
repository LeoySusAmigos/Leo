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

$puntos       = $progreso['puntos'];
$racha        = $progreso['racha'];
$nivel        = $progreso['nivel_actual'];
$leccionActual = $progreso['leccion_actual'];

$porcentajeLeo = min(100, max(0, $progreso['porcentaje']));


/* =========================
   MÓDULO ACTUAL
========================= */

$moduloActual = $progreso['modulo_actual'] ?? 'leo';


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
    SELECT l.titulo, l.descripcion, l.icono
    FROM usuario_logros ul
    JOIN logros l ON ul.logroID = l.logroID
    WHERE ul.userID = '$userID'
    ORDER BY ul.fecha DESC
    LIMIT 1
";

$logro = $conn->query($sql);

if ($logro->num_rows > 0) {
    $logro = $logro->fetch_assoc();
} else {
    $logro = [
        'titulo'      => 'Sin logros aún',
        'descripcion' => 'Completa actividades para obtener tu primer logro',
        'icono'       => 'trofeo.png'
    ];
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
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/navbar.css">
    <link rel="stylesheet" href="styles/inicio-nino.css">
</head>
<body>

    <?php include 'components/navbar.php'; ?>

    <!-- ══════════════════════════════
         HERO DASHBOARD
    ══════════════════════════════ -->
    <section class="hero-dashboard">

        <div class="hero-mascota">
            <img src="images/leito.png">
        </div>

        <div class="hero-info">

            <h1>¡Bienvenido, <?= $usuario['nombre_nino'] ?>!</h1>

            <p>
                Sigues en:
                <?php
                if ($moduloActual == 'leo') {
                    echo "Sílabas con Leo";
                } elseif ($moduloActual == 'capy') {
                    echo "Gramática con Capy";
                } else {
                    echo "Biblioteca con Finx";
                }
                ?>
                — Nivel <?= $nivel ?>
            </p>

            <?php
            $porcentajeGeneral = 0;
            if ($moduloActual == 'leo') {
                $porcentajeGeneral = $porcentajeLeo;
            } elseif ($moduloActual == 'capy') {
                $porcentajeGeneral = $porcentajeCapy;
            } else {
                $porcentajeGeneral = 0;
            }
            ?>

            <div class="barra-progreso">
                <div class="barra-interna" style="width:<?= $porcentajeGeneral ?>%"></div>
            </div>

            <span><?= $porcentajeGeneral ?>% completado</span>

            <a href="#" class="btn-continuar">
                Continuar aventura
                <i class="fa-solid fa-arrow-right"></i>
            </a>

        </div>

    </section>


    <!-- ══════════════════════════════
         AVENTURAS
    ══════════════════════════════ -->
    <section class="aventuras">

        <h2>Elige tu aventura</h2>

        <div class="aventuras-grid"></div>

        <!-- Leo -->
        <div class="aventura leo">
            <img src="images/leito.png">
            <div>
                <h3>Leo</h3>
                <p>Lectura · Nivel <?= $nivel ?></p>

                <div class="mini-barra">
                    <div style="width:<?= $porcentajeLeo ?>%"></div>
                </div>

                <?php if ($porcentajeLeo > 0): ?>
                    <span><?= $porcentajeLeo ?>% completado</span>
                <?php endif; ?>

                <a href="aventura-leo.php" onclick="sessionStorage.setItem('desdeMenuLeo','true')">Ir con Leo</a>
            </div>
        </div>

        <!-- Capy -->
        <div class="aventura capy">
            <img src="images/capy1.png">
            <div>
                <h3>Capy</h3>
                <p>Gramática</p>

                <?php if ($capyDesbloqueado): ?>
                    <div class="mini-barra">
                        <div style="width:<?= $porcentajeCapy ?>%"></div>
                    </div>
                    <a href="aventura2.php">Ir con Capy</a>
                <?php else: ?>
                    <div class="bloqueado">🔒 Completa Leo</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Finx -->
        <div class="aventura finx">
            <img src="images/finx.png">
            <div>
                <h3>Finx</h3>
                <p>Biblioteca</p>

                <?php if ($finxDesbloqueado): ?>
                    <p><?= $cuentosLeidos ?> cuentos leídos</p>
                    <a href="biblioteca.php">Ir con Finx</a>
                <?php else: ?>
                    <div class="bloqueado">🔒 Completa Capy</div>
                <?php endif; ?>
            </div>
        </div>

    </section>


    <!-- ══════════════════════════════
         ESTADÍSTICAS
    ══════════════════════════════ -->
    <section class="estadisticas-nino">

        <div class="card-estadistica">
            ⭐
            <h3><?= $puntos ?></h3>
            <p>Puntos</p>
        </div>

        <div class="card-estadistica">
            🔥
            <h3><?= $racha ?></h3>
            <p>Días seguidos</p>
        </div>

        <div class="card-estadistica">
            📖
            <h3><?= $cuentosLeidos ?></h3>
            <p>Cuentos leídos</p>
        </div>

    </section>


    <!-- ══════════════════════════════
         ÚLTIMO LOGRO
    ══════════════════════════════ -->
    <section class="logro-reciente">

        <div class="logro-icono">🏅</div>

        <div>
            <h3><?= $logro['titulo'] ?></h3>
            <p><?= $logro['descripcion'] ?></p>
        </div>

    </section>


    <!-- ══════════════════════════════
         FOOTER
    ══════════════════════════════ -->
    <section class="dashboard-footer">
        <h2>¡Sigue aprendiendo!</h2>
        <p>Cada lección te acerca más a convertirte en un gran lector.</p>
    </section>


    <script>
        const navToggle = document.getElementById("navToggle");
        const mascotas  = document.getElementById("mascotas");

        navToggle.addEventListener("click", () => {
            mascotas.classList.toggle("active");
        });
    </script>

</body>
</html>