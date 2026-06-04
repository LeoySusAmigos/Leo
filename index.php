<?php session_start(); ?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leo & Friends</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="styles/index.css">
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@700;800&display=swap" rel="stylesheet">
</head>
<body>

    <div class="background-fixed"></div>
    <div class="background-overlay"></div>

    <nav class="navbar navbar-expand-lg navbar-light custom-navbar">

        <div class="container-fluid">
            <a class="navbar-brand" href="index.php"> <img src="images/logonuevo.png" alt="Logo Leo" width="95" height="70"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <?php if(isset($_SESSION['userID'])): ?>
                            <a class="nav-link" href="tus-mascotas.php">Tus mascotas</a>
                        <?php else: ?>
                            <a class="nav-link" href="register.html">Tus mascotas</a>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>
  
                    <li class="nav-item">
                        <a class="nav-link" href="sobre-nosotros.php">Sobre Nosotros</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#">Paquetes Salvajes</a>
                    </li>
                </ul>
            </div>
            

                <ul class="navbar-nav">
                    
                </ul>

                <div class="ms-auto"> 
                <?php if (isset($_SESSION['userID'])): ?> 

                    <div class="player-profile dropdown">

                        <a class="player-btn" href="#"role="button"data-bs-toggle="dropdown"aria-expanded="false">

                            <div class="player-avatar">

                                <i class="fa-solid fa-user"></i>

                            </div>

                            <div class="player-info">

                                

                                <span class="player-name">

                                    <?php echo $_SESSION['nombre_nino'] ?? 'Jugador'; ?>

                                </span>

                            </div>

                        </a>

                        <ul class="dropdown-menu dropdown-menu-end player-menu">

                            <li>
                                <a class="dropdown-item" href="profile.php">

                                    <i class="fa-solid fa-id-card me-2"></i>

                                    Mi Perfil

                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item" href="configuracion.php">

                                    <i class="fa-solid fa-gear me-2"></i>

                                    Configuración

                                </a>
                            </li>

                            <li><hr class="dropdown-divider"></li>

                            <li>
                                <a class="dropdown-item text-danger"
                                href="php/logout.php">

                                    <i class="fa-solid fa-right-from-bracket me-2"></i>

                                    Cerrar Sesión

                                </a>
                            </li>

                        </ul>

                    </div> 

                    <?php else: ?> 
                        <a class="btn login-btn me-2" href="login.html">Iniciar Sesión</a> 
                        <a class="btn register-btn" href="register.html">Registrarse</a> 
                    <?php endif; ?> 
                </div>
            </div>
        </div>

    </nav>

   <section class="hero">

        <div class="container hero-content">

            <div class="hero-text">

                <div class="logo-hero">
                    <img src="images/logo.png" alt="Leo & Friends Logo">
                </div>



                <h2 class="mini-title">
                    Lee, escribe y diviértete con <br>
                    Leo y sus amigos
                </h2>

                <h1 class="hero-title">
                    ¡Aprender 
                    <span id="colora">a</span> 
                    <span>leer</span><br>
                    <p>es una aventura!</p>
                </h1>


                <p class="hero-subtitle">
                    ¡Aprende a leer con diversión!
                </p>

            </div>

            <div class="characters">

                <img src="images/Leito.png" class="character leo">

                <img src="images/Finx.png" class="character finx">

                <img src="images/Capy.png" class="character capy">

            </div>

        </div>

        <div class="blue-strip">

            <h2>
                ¡Descubre nuestras
                <span>funciones!</span>
            </h2>

        </div>

    </section>


    <section class="features container">

        <a href="biblioteca.php" class="feature-card blue-card">
            <div class="icon-circle blue-circle">
                <img src="images/book.png" class="feature-icon">
            </div>
            <h3 id="cuentos">Cuentos</h3>
        </a>

        <a href="tus-mascotas.php" class="card-link">

    <div class="feature-card orange-card">

        <div class="icon-circle orange-circle">
            <img src="images/huella.png" class="feature-icon3">
        </div>

        <h3 id="mascotas">Tus Mascotas</h3>

    </div>

        </a>
        

        <a href="progreso.php" class="card-link">
            <div class="feature-card green-card">

                <div class="icon-circle green-circle">
                    <img src="images/arco.png" class="feature-icon2" alt="Progreso">
                </div>

                <h3 id="progreso">Progreso del niño</h3>

            </div>
        </a>
    </section>

<!-- BOTÓN FINAL -->

    <section class="bottom-button">
        <a href="tus-mascotas.php" class="btn-start">
            ¡Comienza a explorar!
        </a>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>