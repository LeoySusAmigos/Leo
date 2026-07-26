<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>navbar</title>
    <link rel="stylesheet" href="styles/navbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Balsamiq+Sans:wght@700&family=Fredoka:wght@600;900&family=Nunito:wght@700;900&family=Quicksand:wght@500;700;900&display=swap" rel="stylesheet">

</head>

<body>
    <?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    ?>

    <nav class="custom-navbar">
        <div class="nav-container">

            <button class="nav-toggle" id="navToggle" aria-label="Abrir menú" aria-expanded="false">
                <span class="bar"></span>
            </button>

            <div class="logo-container">
                <a href="inicio-nino.php"><img src="images/cartelito.png" alt="logo"></a>
            </div>

            <div class="nav-overlay" id="navOverlay"></div>

            <div class="mascotas-container" id="mascotas">
                <div class="sidebar-header">
                    <span class="sidebar-title">Menú</span>
                    <button class="sidebar-close" id="sidebarClose" aria-label="Cerrar menú">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <ul class="nav-menu">
                    <li class="nav-element">
                        <a href="aventura1.html"><img src="images/Leito.png" alt="Leo" class="mascota-img"></a>
                        <a class="menu-link" href="aventura-leo.php">Sílabas con Leo</a>
                    </li>
                    <li class="nav-element">
                        <a href="aventura2.php"><img src="images/capy1.png" alt="Capy" class="mascota-img"></a>
                        <a class="menu-link" href="aventura2.php">Gramática con Capy</a>
                    </li>
                    <li class="nav-element">
                        <a href="biblioteca.php"><img src="images/FinxHi.png" alt="Finx" class="mascota-img"></a>
                        <a class="menu-link" href="biblioteca.php">Lectura con Finx</a>
                    </li>
                </ul>

                <a href="inicio-nino.php" class="sidebar-home">
                    <i class="fa-solid fa-house"></i>
                    Volver al inicio
                </a>
            </div>

            <div class="usuario-container">
                <?php if (isset($_SESSION['userID'])): ?>

                    <!-- Al hacer clic en el nombre/avatar va a profile.php -->
                    <a href="profile.php" class="profile-box" style="text-decoration:none;">
                        <div class="avatar-circle">
                            <?php if (!empty($_SESSION['foto_nino'])): ?>
                                <!-- Si el usuario ya subió foto, la muestra -->
                                <img src="images/perfiles/<?= htmlspecialchars($_SESSION['foto_nino']) ?>"
                                    alt="Foto de <?= htmlspecialchars($_SESSION['nombre_nino']) ?>"
                                    style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                            <?php else: ?>
                                <!-- Si no tiene foto, ícono genérico -->
                                <i class="fa-solid fa-user"></i>
                            <?php endif; ?>
                        </div>
                        <div class="user-info">
                            <span class="user-name"><?= htmlspecialchars($_SESSION['nombre_nino']) ?></span>
                            <span class="user-points">
                                <i class="fa-solid fa-star star-icon"></i>
                                <?= isset($_SESSION['puntos']) ? $_SESSION['puntos'] : '0' ?>
                            </span>
                        </div>
                    </a>

                    <!-- Tuerquita de configuración, a la derecha del perfil -->
                    <a href="configuracion.php" class="navbar-gear" title="Configuración">
                        <i class="fa-solid fa-gear"></i>
                    </a>

                <?php else: ?>
                    <a class="btn-login" href="login.html">Login</a>
                    <a class="btn-register" href="register.html">Registrarse</a>
                <?php endif; ?>
            </div>

        </div>
    </nav>

    <script src="js/navbar.js"></script>
</body>

</html>