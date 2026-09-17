<?php
session_start();

if (!isset($_SESSION['userID'])) {
    header("Location: login.html");
    exit();
}

include 'php/conexion.php';

$userID = $_SESSION['userID'];


$sql = "SELECT * FROM capy_lecciones
        WHERE activa = 1
        ORDER BY nivel ASC, numero_leccion ASC";

$resultado = $conn->query($sql);

if (!$resultado) {
    die("Error al cargar las lecciones de Capy: " . $conn->error);
}

$leccionesPorNivel = [];

while ($leccion = $resultado->fetch_assoc()) {
    $leccionesPorNivel[$leccion['nivel']][] = $leccion;
}


$nivelesDesbloqueados = [
    1 => true,
    2 => false,
    3 => false,
    4 => false,
    5 => false
];

$sqlDesbloqueo = "
    SELECT
        l.nivel,
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
    WHERE l.activa = 1
    GROUP BY l.nivel
    ORDER BY l.nivel ASC
";

$stmtDesbloqueo = $conn->prepare($sqlDesbloqueo);

if (!$stmtDesbloqueo) {
    die("Error al comprobar el desbloqueo de niveles: " . $conn->error);
}

$stmtDesbloqueo->bind_param("i", $userID);
$stmtDesbloqueo->execute();

$resultadoDesbloqueo = $stmtDesbloqueo->get_result();

$progresoNiveles = [];

while ($fila = $resultadoDesbloqueo->fetch_assoc()) {
    $nivel = (int)$fila['nivel'];

    $progresoNiveles[$nivel] = [
        'total' => (int)$fila['total_lecciones'],
        'completadas' => (int)$fila['lecciones_completadas']
    ];
}

$stmtDesbloqueo->close();


for ($nivel = 2; $nivel <= 5; $nivel++) {

    $nivelAnterior = $nivel - 1;

    $totalAnterior =
        $progresoNiveles[$nivelAnterior]['total'] ?? 0;

    $completadasAnterior =
        $progresoNiveles[$nivelAnterior]['completadas'] ?? 0;

    if (
        $totalAnterior > 0 &&
        $completadasAnterior >= $totalAnterior
    ) {
        $nivelesDesbloqueados[$nivel] = true;
    }
}


$niveles = [
    1 => [
        'titulo' => 'Los sustantivos',
        'descripcion' => 'Aprende a reconocer personas, animales, lugares y objetos.'
    ],
    2 => [
        'titulo' => 'Los artículos',
        'descripcion' => 'Aprende a combinar artículos y sustantivos.'
    ],
    3 => [
        'titulo' => 'Los adjetivos',
        'descripcion' => 'Descubre cómo describir personas, animales y objetos.'
    ],
    4 => [
        'titulo' => 'Los verbos',
        'descripcion' => 'Aprende a reconocer y usar acciones.'
    ],
    5 => [
        'titulo' => '¡Construimos oraciones!',
        'descripcion' => 'Usa todo lo aprendido para crear oraciones completas.'
    ]
];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aventura de Capy</title>

    <link rel="shortcut icon" href="images/favicon/favicon-32x32.png" type="image/x-icon">
    <link rel="stylesheet" href="styles/capy.css">
    <link rel="stylesheet" href="styles/navbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700&family=Quicksand:wght@500;600;700&display=swap" rel="stylesheet">
</head>

<body>

    <?php include 'navbar.php'; ?>

    <div class="capy-container">
        <header class="capy-header">
            <div class="capy-title">
                <h1>AVENTURA DE CAPY</h1>
                <p>
                    Aprende a construir tus propias oraciones
                </p>
            </div>
        </header>

        <div class="tabs-mascotas-container">
            <a href="aventura-leo.php" class="tab-item">
                <img src="images/Leito.png" alt="Leo" class="tab-mascota-img">

                <div class="tab-text">
                    <span class="tab-title">
                        Lectura y vocabulario
                    </span>
                    <span class="tab-subtitle text-leo">
                        Leo
                    </span>
                </div>
            </a>

            <div class="tab-item active">
                <img src="images/capy1.png" alt="Capy" class="tab-mascota-img">

                <div class="tab-text">
                    <span class="tab-title">
                        Gramática y oraciones
                    </span>
                    <span class="tab-subtitle text-capy">
                        Capy
                    </span>
                </div>
            </div>


            <a href="biblioteca.php" class="tab-item">
                <img src="images/finxito3.png" alt="Finx" class="tab-mascota-img">

                <div class="tab-text">
                    <span class="tab-title">
                        Cuentos y comprensión
                    </span>
                    <span class="tab-subtitle text-finx">
                        Finx
                    </span>
                </div>
            </a>
        </div>


        <main class="capy-content">
            <div class="capy-banner">
                <div class="capy-banner-icon">
                    <i class="fa-solid fa-puzzle-piece"></i>
                </div>

                <div class="capy-banner-text">
                    <strong>
                        ¡Construyamos juntos!
                    </strong>
                    <p>
                        Aprende nuevas palabras y úsalas para crear
                        oraciones cada vez más completas.
                    </p>
                </div>
            </div>


            <?php for ($nivel = 1; $nivel <= 5; $nivel++): ?>

                <?php
                    $infoNivel = $niveles[$nivel];
                    $lecciones = $leccionesPorNivel[$nivel] ?? [];

                    $desbloqueado = $nivelesDesbloqueados[$nivel] ?? false;
                ?>

                <section
                    class="capy-level <?php echo $desbloqueado ? 'nivel-desbloqueado' : 'nivel-bloqueado'; ?>"
                    data-desbloqueado="<?php echo $desbloqueado ? '1' : '0'; ?>"
                >

                    <div class="level-header">

                        <div class="level-number">
                            Nivel <?php echo $nivel; ?>
                        </div>

                        <div class="level-information">

                            <h2>
                                <?php echo htmlspecialchars($infoNivel['titulo']); ?>
                            </h2>

                            <p>
                                <?php echo htmlspecialchars($infoNivel['descripcion']); ?>
                            </p>

                        </div>

                        <div class="level-state">

                            <?php if (!$desbloqueado): ?>

                                <i class="fa-solid fa-lock level-lock"></i>

                            <?php endif; ?>

                            <i
                                class="fa-solid <?php echo $desbloqueado ? 'fa-chevron-up' : 'fa-chevron-down'; ?> level-arrow"
                            ></i>

                        </div>

                    </div>


                    <div class="lessons-container">

                        <?php if (!$desbloqueado): ?>

                            <div class="level-locked-message">

                                <i class="fa-solid fa-lock"></i>

                                <div>
                                    <strong>Nivel bloqueado</strong>

                                    <span>
                                        Completa todas las lecciones del nivel anterior para desbloquearlo.
                                    </span>
                                </div>

                            </div>

                        <?php else: ?>

                            <?php foreach ($lecciones as $leccion): ?>

                                <a
                                    href="leccion-capy.php?id=<?php echo (int)$leccion['id']; ?>"
                                    class="lesson-card"
                                >

                                    <div class="lesson-icon">

                                        <i class="<?php echo htmlspecialchars($leccion['icono']); ?>"></i>

                                    </div>

                                    <div class="lesson-info">

                                        <h3>
                                            <?php echo htmlspecialchars($leccion['titulo']); ?>
                                        </h3>

                                        <span>
                                            <?php echo htmlspecialchars($leccion['descripcion']); ?>
                                        </span>

                                    </div>

                                    <div class="lesson-status">
                                        <span></span>
                                    </div>

                                </a>

                            <?php endforeach; ?>

                        <?php endif; ?>

                    </div>

                </section>

            <?php endfor; ?>
        </main>
    </div>

    <script src="js/capy.js"></script>

</body>

</html>