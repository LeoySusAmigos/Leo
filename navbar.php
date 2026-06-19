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

    <nav class="custom-navbar">
        <div class="nav-container">

            <div class="logo-container">
                <a class="logo-text leo" href="index.php">Leo</a>
                <a class="logo-text and" href="index.php">&</a>
                <a class="logo-text friends" href="index.php">Friends</a>
            </div>

            <button class="nav-toggle" id="navToggle" aria-label="Abrir menú">
                <i class="fa-solid fa-bars"></i>
            </button>

            <div class="mascotas-container" id="mascotas">
                <ul class="nav-menu">
                    <li class="nav-element">
                        <a href="aventura1.html"><img src="images/Leito.png" alt="Leo" class="mascota-img"></a>
                        <a class="menu-link" href="aventura1.html">Sílabas con Leo</a>
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
            </div>

            <div class="usuario-container">
                <?php if (isset($_SESSION['userID'])): ?>
                    <div class="profile-box">
                        <div class="avatar-circle">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <div class="user-info">
                            <span class="user-name"><?php echo htmlspecialchars($_SESSION['nombre_nino']); ?></span>
                            <span class="user-points">
                                <i class="fa-solid fa-star star-icon"></i>
                                <?php echo isset($_SESSION['puntos']) ? $_SESSION['puntos'] : '0'; ?>
                            </span>
                        </div>
                    </div>


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