<?php
session_start();

if (!isset($_SESSION['userID'])) {
    header("Location: register.html");
    exit();
}

include 'php/conexion.php';
$userID = $_SESSION['userID'];

$libros_leidos = array();
$sql_progreso = "SELECT libro_id FROM progreso_libros WHERE userID = $userID";
$res_progreso = mysqli_query($conn, $sql_progreso);

if ($res_progreso) {
    while ($progreso = mysqli_fetch_assoc($res_progreso)) {
        $libros_leidos[] = $progreso['libro_id'];
    }
}

$sql_libros = "SELECT * FROM libros ORDER BY nivel_id ASC, libro_id ASC";
$res_libros = mysqli_query($conn, $sql_libros);

if (!$res_libros) {
    die("Error en la consulta: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mini Biblioteca</title>
    <link rel="stylesheet" href="styles/biblioteca.css">
    <link rel="stylesheet" href="styles/navbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600;700&family=Quicksand:wght@500;700&display=swap" rel="stylesheet">
</head>

<body>

    <?php include 'components/navbar.php'; ?>

    <div class="biblioteca-container">

        <header class="biblio-header-modern"> 
            <div class="header-center-title">
                <h1>MINI BIBLIOTECA</h1>
                <p>Aprender a leer es una aventura</p>
            </div>
            
            <div class="filtrar">
                <div class="filtro-dropdown">
                    <button class="btn-filtro-modern">
                        Filtrar <i class="fa-solid fa-filter"></i>
                    </button>

                    <div class="menu-filtro" style="display: none;">
                    <div class="opcion" data-nivel="todos">Todos</div>
                    <div class="opcion" data-nivel="1">Nivel 1</div>
                    <div class="opcion" data-nivel="2">Nivel 2</div>
                    <div class="opcion" data-nivel="3">Nivel 3</div>
                    <div class="opcion" data-nivel="4">Nivel 4</div>
                    <div class="opcion" data-nivel="5">Nivel 5</div>
                </div>
            </div>

        </header>

        <div class="tabs-mascotas-container">
            <a href="aventura1.html" class="tab-item">
                <img src="images/Leito.png" alt="Leo" class="tab-mascota-img">
                <div class="tab-text text-leo">
                    <span class="tab-title text-secondary">Comprensión y vocabulario</span>
                    <span class="tab-subtitle">Leo</span>
                </div>
            </a>    

            <a href="aventura2.php" class="tab-item">
                <img src="images/capy1.png" alt="Capy" class="tab-mascota-img">
                <div class="tab-text text-capy">
                    <span class="tab-title text-secondary">Gramática y oraciones</span>
                    <span class="tab-subtitle">Capy</span>
                </div>
            </a>
            
            <div class="tab-item active">
                <img src="images/FinxHi.png" alt="Finx" class="tab-mascota-img">
                <div class="tab-text text-finx">
                    <span class="tab-title text-success">Cuentos</span>
                    <span class="tab-subtitle">Finx</span>
                </div>
            </div>
        </div>

        <main class="content-wrapper-modern">

            <div class="info-banner-niveles">
                <div class="banner-icon-circle">
                    <i class="fa-solid fa-book-open"></i>
                </div>
                <div class="banner-info-text">
                    <strong>Una aventura en cada página.</strong>
                    <p>Cada vez que abres un libro, comienza una nueva aventura. Sigue leyendo y descubre los tesoros que te esperan en cada página.</p>
                </div>
            </div>

            <div class="estanteria-por-niveles">
                <?php 
                $nivel_actual = 0; 
                $id_libro_anterior = 0; 

                if (mysqli_num_rows($res_libros) > 0) {
                    while ($libro = mysqli_fetch_assoc($res_libros)) {
              
                        if ($libro['nivel_id'] != $nivel_actual) {
                            
                            if ($nivel_actual != 0) {
                                echo '</div></div>'; 
                            }
                            
                            $nivel_actual = $libro['nivel_id'];
                           
                            $subtitulo = "4 líneas • Palabras simples";
                            if ($nivel_actual == 2) { $subtitulo = "6 líneas • Una idea por oración"; }
                            if ($nivel_actual == 3) { $subtitulo = "2 páginas • Comprensión fluida"; }
                            if ($nivel_actual == 4) { $subtitulo = "4 páginas • Vocabulario avanzado"; }
                            if ($nivel_actual == 5) { $subtitulo = "6 páginas • Textos narrativos completos • Desafío máximo"; }
                            ?>
                            
                            <div class="nivel-row-container">
                                <div class="nivel-row-header">
                                    <div class="nivel-badge-pill">Nivel <?php echo $nivel_actual; ?></div>
                                    <span class="nivel-meta-info"><?php echo $subtitulo; ?></span>
                                    <i class="fa-solid fa-chevron-up toggle-row-icon"></i>
                                </div>
                                <div class="nivel-row-cards-flex">
                            <?php
                        }

                        $ya_leido = in_array($libro['libro_id'], $libros_leidos);

                        $esta_bloqueado = false;
                        if ($id_libro_anterior != 0 && !in_array($id_libro_anterior, $libros_leidos)) {
                            $esta_bloqueado = true;
                        }
                        ?>

                        <?php if ($esta_bloqueado): ?>
                            <div class="card-cuento-modern locked">
                                <div class="card-left-thumb">
                                    <img src="images/cuentos/<?php echo $libro['portada']; ?>" alt="Bloqueado" class="img-cuento-blur">
                                </div>
                                <div class="card-center-data">
                                    <h4><?php echo htmlspecialchars($libro['titulo']); ?></h4>
                                    <span class="duration-text"><i class="fa-regular fa-clock"></i> <?php echo $libro['tiempo_estimado']; ?> min</span>
                                    <p class="mission-alert-text">Completa el cuento anterior</p>
                                </div>
                                <div class="card-right-status">
                                    <span class="status-lock-icon"><i class="fa-solid fa-lock text-muted"></i></span>
                                </div>
                            </div>
                        <?php else: ?>
                            <a href="leer-libro.php?id=<?php echo $libro['libro_id']; ?>" class="card-cuento-modern-link">
                                <div class="card-cuento-modern <?php echo $ya_leido ? 'completed' : ''; ?>">
                                    <div class="card-left-thumb">
                                        <img src="images/cuentos/<?php echo $libro['portada']; ?>" alt="Portada">
                                    </div>
                                    <div class="card-center-data">
                                        <h4><?php echo htmlspecialchars($libro['titulo']); ?></h4>
                                        <span class="duration-text"><i class="fa-regular fa-clock"></i> <?php echo $libro['tiempo_estimado']; ?> min</span>
                                    </div>
                                    <div class="card-right-status">
                                        <?php if ($ya_leido): ?>
                                            <span class="status-check-circle completed"><i class="fa-solid fa-check"></i></span>
                                        <?php else: ?>
                                            <span class="status-check-circle empty"></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </a>
                        <?php 
                        endif;

                        $id_libro_anterior = $libro['libro_id'];
                    }
                    
                    echo '</div></div>';
                } else {
                    echo '<p class="text-muted text-center p-4">Aún no hay cuentos registrados en la plataforma.</p>';
                }
                ?>

            </div>

        </main>
        
    </div>

    <div class="mascota">
        <img src="images/FinxHi.png" alt="Finx" class="mascota-img">
    </div>

    <script src="js/index.js"></script>
    <script src="js/navbar.js"></script>
</body>

</html>