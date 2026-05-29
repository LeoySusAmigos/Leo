<?php
session_start();

if (!isset($_SESSION['userID'])) {
    header("Location: register.html");
    exit();
}

include 'php/conexion.php';

// Consultas limpias sin caracteres invisibles
$res_niveles = mysqli_query($conn, "SELECT * FROM cuentos WHERE categoria = 'principal' ORDER BY orden ASC");
$res_nuevos  = mysqli_query($conn, "SELECT * FROM cuentos WHERE categoria = 'nuevo' ORDER BY orden ASC");

if (!$res_niveles || !$res_nuevos) {
    die("Error en la consulta: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mini Biblioteca - Leo & Friends</title>
    <link rel="stylesheet" href="styles/biblioteca.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Balsamiq+Sans:wght@700&family=Fredoka:wght@600;900&family=Nunito:wght@700;900&family=Quicksand:wght@500;700;900&display=swap" rel="stylesheet">
</head>
<body>

    <div class="biblioteca-container">
        
        <a href="index.php" class="btn-back">
            <img src="images/arrow-left.png" alt="Volver">
        </a>

        <div class="top-bar">
            <div class="coin-display">
                <img src="images/coin.png" alt="Moneda">
                <span><?php echo isset($_SESSION['monedas']) ? $_SESSION['monedas'] : 50; ?></span>
            </div>
            
        </div>
        
        <header class="biblio-header">
            <div class="banner-titulo">
                <h1>MINI BIBLIOTECA</h1>
                <p>aprender es una aventura</p>
            </div>
            <div class="sub-banner">
                <span>¡Lee, aprende y diviértete con Finx!</span>
            </div>
        </header>

        <main class="content-wrapper">
            
            <div class="personaje-guia">
                <div class="bocadillo">
                    <p><span class="saludo">¡Hola, amiguito!</span><br>Aquí encontrarás cuentos para aprender, leer y divertirte. ❤️</p>
                </div>
                <img src="images/FinxHi.png" alt="Finx" class="img-finx">
            </div>

            <div class="estanteria">

                <div class="filtrar">
                    
                    <div class="filtro-dropdown">

                        <button class="btn-filtro">
                            Filtro ▼
                        </button>

                        <div class="menu-filtro">

                            <div class="opcion" data-nivel="todos">Todos</div>
                            <div class="opcion" data-nivel="1">Nivel 1</div>
                            <div class="opcion" data-nivel="2">Nivel 2</div>
                            <div class="opcion" data-nivel="3">Nivel 3</div>
                            <div class="opcion" data-nivel="4">Nivel 4</div>
                            <div class="opcion" data-nivel="5">Nivel 5</div>

                        </div>

                    </div>
                </div>    

                <div class="grid-niveles">
                    <button class="nav-arrow prev-arrow">&#10094;</button> 

                    <div class="cards-container-niveles">
                        <?php if (mysqli_num_rows($res_niveles) > 0): ?>
                            <?php while($libro = mysqli_fetch_assoc($res_niveles)): ?>
                                <a href="leer_cuento.php?id=<?php echo $libro['cuentoID']; ?>" class="card-link">
                                    <div class="card card-nivel-<?php echo $libro['nivel']; ?>">
                                        
                                        <div class="badge-nivel">Nivel <?php echo $libro['nivel']; ?></div>
                                        
                                        <h3 class="titulo-cuento"><?php echo $libro['titulo']; ?></h3>
                                        
                                        <div class="wrapper-imagen">
                                            <img src="images/cuentos/<?php echo $libro['imagen']; ?>" alt="Portada Cuento">
                                        </div>

                                    </div>
                                </a>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="no-data">No hay libros disponibles en esta categoría.</p>
                        <?php endif; ?>
                    </div>

                    <button class="nav-arrow next-arrow">&#10095;</button> 
                </div>

                <div class="grid-nuevos">
                    <?php if (mysqli_num_rows($res_nuevos) > 0): ?>
                        <?php while($nuevo = mysqli_fetch_assoc($res_nuevos)): ?>
                            <button id="buy-book">
                                <div class="card-mini card-nuevo-<?php echo $nuevo['nivel']; ?>">
                                    
                                    <div class="badge-mini-nuevo">Nuevo</div>
                                    
                                    <h4 class="titulo-mini-cuento"><?php echo $nuevo['titulo']; ?></h4>
                                    
                                    <div class="wrapper-imagen-mini">
                                        <img src="images/cuentos/<?php echo $nuevo['imagen']; ?>" alt="Portada Cuento" class="img-locked">
                                    </div>
                                    
                                    <div class="footer-card-lock">
                                        <span class="precio-texto"><?php echo $nuevo['precio_monedas']; ?> Monedas</span>
                                        <span class="icon-lock">🔒</span>
                                    </div>

                                </div>
                            </button>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="no-data">No hay cuentos nuevos por ahora.</p>
                    <?php endif; ?>
                </div>

            </div> 
        
        </main>

    </div> 

    <script src="js/index.js"></script>

</body>
</html>