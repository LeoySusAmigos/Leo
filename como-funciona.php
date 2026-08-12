<?php

session_start();

include("php/conexion.php");

?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Como funciona - Leo &amp; Friends</title>
    <link rel="stylesheet" href="styles/como-funciona.css">
    <link rel="shortcut icon" href="images/favicon/favicon-32x32.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/navbar1.css">
    <link rel="stylesheet" href="styles/index.css">
</head>
<body>

  <?php include("navbar1.php"); ?>

<section class="carrusel container-1" id="carrusel">
  <div class="carrusel-contenido">

    <div class="carrusel-texto">
      <span class="carrusel-etiqueta">COMO FUNCIONA</span>
      <h2 id="carruselTitulo">Conoce cómo funciona Leo &amp; Friends</h2>
      <p id="carruselTexto">Descubre, paso a paso, como nuestra plataforma acompana el aprendizaje de los ninos de forma interactiva, divertida y efectiva.</p>
      <a href="register.html" class="carrusel-boton">Comienza hoy gratis</a>

      <div class="carrusel-flechas">
        <button id="carruselAnterior" type="button">&#8592;</button>
        <button id="carruselSiguiente" type="button">&#8594;</button>
      </div>
    </div>

    <div class="carrusel-video">
      <img src="images/leo-senalando.png" alt="Leo el camaleon" class="carrusel-mascota" id="carruselMascota">
      <video id="carruselVideo" controls poster="">
        <source id="carruselFuente" src="" type="video/mp4">
      </video>
    </div>

  </div>

  <div class="carrusel-progreso">
    <p class="carrusel-contador"><span id="carruselNumero">01</span> / 05</p>

    <div class="carrusel-pasos">
      <div class="carrusel-linea">
        <div class="carrusel-linea-relleno" id="carruselRelleno"></div>
      </div>

      <button
        type="button"
        class="carrusel-paso activo"
        data-paso="0"
        data-titulo="Conoce cómo funciona Leo &amp; Friends"
        data-texto="Descubre, paso a paso, como nuestra plataforma acompaña el aprendizaje de los niños de forma interactiva, divertida y efectiva."
        data-video="videos/paso-01-inicio.mp4"
        data-mascota="images/leo-carrusel.png"
        data-mascota-alt="Leo el camaleon">
        <span class="carrusel-paso-circulo"><i class="fa-solid fa-house carrusel-paso-icono"></i><span class="carrusel-paso-numero">01</span></span>
        <span class="carrusel-paso-label">Inicio</span>
      </button>
      <button
        type="button"
        class="carrusel-paso"
        data-paso="1"
        data-titulo="Niveles de Leo"
        data-texto="Comienza desde lo más sencillo y avanza hacia nuevos retos. Con Leo, los niños fortalecen sus habilidades de lectura y aprenden las vocales mientras progresan a través de diferentes niveles."
        data-video=""
        data-mascota="images/leo-carrusel1.png"
        data-mascota-alt="Leo">
        <span class="carrusel-paso-circulo"><i class="fa-solid fa-magnifying-glass carrusel-paso-icono"></i><span class="carrusel-paso-numero">02</span></span>
        <span class="carrusel-paso-label">Niveles Leo</span>
      </button>
      <button
        type="button"
        class="carrusel-paso"
        data-paso="2"
        data-titulo="Actividades para reforzar lo aprendido"
        data-texto="Pon a prueba lo aprendido con actividades interactivas de gramática. Capy acompaña a los niños en retos diseñados para aprender de forma divertida y dinámica."
        data-video="videos/paso-03-actividades.mp4"
        data-mascota="images/capy-carrusel.png"
        data-mascota-alt="Capy">
        <span class="carrusel-paso-circulo"><i class="fa-solid fa-spell-check carrusel-paso-icono"></i><span class="carrusel-paso-numero">03</span></span>
        <span class="carrusel-paso-label">Actividades con Capy</span>
      </button>
      <button
        type="button"
        class="carrusel-paso"
        data-paso="3"
        data-titulo="Una biblioteca hecha para explorar"
        data-texto="Explora cuentos educativos con Finx y refuerza lo aprendido con actividades interactivas, como un rompecabezas basado en la portada de cada cuento."
        data-video=""
        data-mascota="images/finx-carrusel.png"
        data-mascota-alt="Mascota de biblioteca">
        <span class="carrusel-paso-circulo"><i class="fa-solid fa-book carrusel-paso-icono"></i><span class="carrusel-paso-numero">04</span></span>
        <span class="carrusel-paso-label">Biblioteca de Finx</span>
      </button>
      <button
        type="button"
        class="carrusel-paso"
        data-paso="4"
        data-titulo="Progreso: acompaña cada paso de su aprendizaje"
        data-texto="Consulta los avances de tu hijo, reconoce sus fortalezas e identifica las áreas que necesitan mayor refuerzo. Así podrás acompañar de cerca su aprendizaje."
        data-video="videos/paso-05-padres.mp4"
        data-mascota="images/leoandfriends-carrusel.png"
        data-mascota-alt="Mascotitas">
        <span class="carrusel-paso-circulo"><i class="fa-solid fa-chart-line carrusel-paso-icono"></i><span class="carrusel-paso-numero">05</span></span>
        <span class="carrusel-paso-label">Progreso</span>
      </button>
    </div>
  </div>
</section>

<section class="carrusel-confianza">
  <span class="carrusel-confianza-icono">
    <i class="fa-solid fa-shield-halved"></i>
  </span>
  <p class="carrusel-confianza-texto">Un entorno seguro y educativo, diseñado para inspirar a cada niño a aprender y crecer con confianza.</p>
  <div class="carrusel-confianza-mascotas">
    <img src="images/leo&friendsCarrusel.png" alt="Leo&friends">
  </div>
</section>

<script src="js/como-funciona.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>