<?php

session_start();

include("php/conexion.php");

?>

<!DOCTYPE html>

<html lang="es">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Reseñas | Leo & Friends</title>

<link rel="stylesheet" href="styles/resenas.css">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700&family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

</head>

<body>

<nav class="navbar navbar-expand-lg custom-navbar">

        <div class="container">

            <!-- LOGO -->

            <a class="navbar-brand logo" href="index.php">

                <span class="leo">Leo</span>

                <span class="ampersand">&</span>

                <span class="friends">Friends</span>

            </a>

            <!-- BOTÓN RESPONSIVE -->

            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarNav">

                <span class="navbar-toggler-icon"></span>

            </button>

            <div class="collapse navbar-collapse" id="navbarNav">

                <!-- MENÚ CENTRADO -->

                <ul class="navbar-nav mx-auto menu-center">

                    <li class="nav-item">

                        <a class="nav-link" href="#">
                            Cómo funciona
                        </a>

                    </li>

                    <li class="nav-item">

                        <a class="nav-link" href="sobre-nosotros.php">
                            Sobre Nosotros
                        </a>

                    </li>

                    <li class="nav-item">

                        <a class="nav-link" href="#">
                            Paquetes Salvajes
                        </a>

                    </li>

                    <li class="nav-item">

                        <a class="nav-link" href="resenas.php">
                            Reseñas
                        </a>

                    </li>

                </ul>

                <!-- DERECHA -->

                <div class="nav-buttons">

                    <?php if (isset($_SESSION['userID'])): ?>

                        <div class="dropdown">

                            <a
                            class="btn profile-btn dropdown-toggle"

                            href="#"

                            data-bs-toggle="dropdown">

                                <i class="fa-solid fa-circle-user"></i>

                                <?php echo $_SESSION['nombre_nino']; ?>

                            </a>

                            <ul class="dropdown-menu dropdown-menu-end">

                                <li>

                                    <a class="dropdown-item"

                                    href="profile.php">

                                        Perfil

                                    </a>

                                </li>

                                <li>

                                    <hr class="dropdown-divider">

                                </li>

                                <li>

                                    <a

                                    class="dropdown-item text-danger"

                                    href="php/logout.php">

                                        Cerrar sesión

                                    </a>

                                </li>

                            </ul>

                        </div>

                    <?php else: ?>

                        <a class="btn login-btn"

                        href="login.html">

                            Iniciar sesión

                        </a>

                        <a class="btn register-btn"

                        href="register.html">

                            Registrarse gratis

                        </a>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </nav>


<!-- CONTENEDOR PRINCIPAL -->

<div class="contenedor-resenas">


<!-- TITULO -->

<div class="encabezado">

<h1>

<i class="fa-solid fa-seedling"></i>

Reseñas de nuestra comunidad

</h1>

<p>

Conoce la experiencia de otros padres con Leo & Friends

</p>

</div>


<!-- CONTENIDO -->

<div class="contenido">


<!-- LADO IZQUIERDO -->

<div class="lado-izquierdo">


<!-- FILTRO -->

<div class="filtro">

<select>

<option>Más recientes</option>

<option>Mejor calificadas</option>

<option>Más antiguas</option>

</select>

</div>


<!-- TARJETAS -->

<div class="grid-resenas">

<?php include("php/obtener_resenas.php"); ?>

</div>

</div>


<!-- LADO DERECHO -->

<div class="lado-derecho">


<!-- ESCRIBIR RESEÑA -->

<div class="escribir-resena">

<h2>

<i class="fa-solid fa-pen"></i>

Escribe tu reseña

</h2>

<p>

Comparte tu experiencia y ayuda a otros padres.

</p>

<div class="requisitos">

<p>✅ Usar la plataforma 7 días o más</p>

<p>✅ Completar al menos 5 lecciones</p>

</div>

<a href="php/guardar_resena.php"

class="btn-resena">

Escribir reseña

</a>

</div>


<!-- ESTADÍSTICAS -->

<div class="estadisticas">

<h2>Calificación promedio</h2>

<h3>4.8</h3>

<div class="estrellas">

★★★★★

</div>

<p>Basado en 128 reseñas</p>

</div>

</div>

</div>


<!-- BANNER FINAL -->

<div class="banner">

<img src="images/leito.png">

<div>

<h2>¡Únete a Leo & Friends!</h2>

<p>

Miles de niños ya están aprendiendo a leer.

</p>

</div>

<a href="register.html">

Comenzar gratis

</a>

</div>

</div>

</body>

</html>