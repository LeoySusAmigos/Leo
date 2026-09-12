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
                ?>

                <section class="capy-level">
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

                        <i class="fa-solid fa-chevron-up level-arrow"></i>
                    </div>


                    <div class="lessons-container">
                        <?php foreach ($lecciones as $leccion): ?>
                            <a href="leccion-capy.php?id=<?php echo (int)$leccion['id']; ?>" class="lesson-card">

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
                    </div>
                </section>
            <?php endfor; ?>
        </main>
    </div>

    <script src="js/capy.js"></script>

</body>

</html>