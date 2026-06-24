<?php

session_start();

include("php/conexion.php");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leo & Friends</title>
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
<<<<<<< HEAD
   
    <?php include("navbar1.php"); ?>

=======
    <nav class="navbar navbar-expand-lg custom-navbar">
        <div class="container">
 
            <!-- LOGO -->
 
            <p>
                Leo y sus amigos guían a tu hijo paso a paso,
                con juegos, cuentos y métodos que funcionan.
            </p>
 
            <a href="register.html" class="hero-btn">
                Comenzar gratis
            </a>
        </div>
 
        <div class="hero-characters">
            <img src="images/capy1.png" alt="Capy">
            <img src="images/leito.png" alt="Leo">
            <img src="images/finx.png" alt="Finx">
        </div>
    </section>
 
    <!-- BENEFICIOS -->
 
    <section class="beneficios">
 
        <h2>¿Por qué Leo & Friends?</h2>
 
        <div class="beneficios-grid">
 
            <!-- Card 1 -->
 
            <div class="beneficio">
 
                <div class="benefit-icon">
 
                    <i class="bi bi-book"></i>
 
                </div>
 
                <h3>Método probado</h3>
 
                <p>
 
                    Basado en la metodología de los 20 días,
 
                    con progresión silábica estructurada.
 
                </p>
 
            </div>
 
            <!-- Card 2 -->
 
            <div class="beneficio">
 
                <div class="benefit-icon">
 
                    <i class="bi bi-controller"></i>
 
                </div>
 
                <h3>Aprendizaje con juego</h3>
 
                <p>
 
                    Cuentos interactivos, juegos y mascotas
 
                    que motivan al niño a seguir.
 
                </p>
 
            </div>
 
            <!-- Card 3 -->
 
            <div class="beneficio">
 
                <div class="benefit-icon">
 
                    <i class="bi bi-graph-up"></i>
 
                </div>
 
                <h3>Seguimiento para padres</h3>
 
                <p>
 
                    Reportes claros sobre el avance,
 
                    dificultades y logros.
 
                </p>
 
            </div>
 
        </div>
 
    </section>
 
 
<!-- METODOLOGÍA -->
 
    <section class="metodologia">
 
        <h2>Un método paso a paso que sí funciona</h2>
 
        <div class="metodologia-contenedor">
 
            <!-- FUNDAMENTOS -->
 
            <div class="etapa">
 
                <div class="encabezado-etapa">
 
                    <span class="numero">1</span>
 
                    <div class="contenido-etapa">
 
                        <h3>Fundamentos</h3>
 
                        <p>
                            Letras y sílabas con audio.
                            El niño escucha, repite y reconoce.
                        </p>
 
                    </div>
                </div>
                
                <div class="elemento-etapa">
 
                        <div class="audio-silaba">
 
                            <button
 
                            class="boton-audio"
 
                            onclick="reproducirAudio('audio-ma')">
 
                                <i class="fa-solid fa-play"></i>
 
                            </button>
 
                            <span>ma</span>
 
                        </div>
 
                        <audio id="audio-ma">
 
                            <source src="audios/ma.mp3">
 
                        </audio>
 
                    </div>
 
            </div>
 
 
 
            <!-- CONSTRUCCIÓN -->
 
            <div class="etapa">
                
                <div class="encabezado-etapa">
 
                    <span class="numero">2</span>
 
                    <div class="contenido-etapa">
 
                        <h3>Construcción</h3>
 
                        <p>
                            Gramática simple y formación
                            de oraciones con Capy.
                        </p>
 
                    </div>           
 
                </div>
 
                <div class="elemento-etapa">
 
                    <img src="images/oracioncapy.png" class="imagen-oracion" alt="Capy">
 
                </div>
 
            </div>
 
 
 
            <!-- DOMINIO -->
 
            <div class="etapa">
                <div class="encabezado-etapa">
 
                    <span class="numero">3</span>
 
                    <div class="contenido-etapa">
 
                        <h3>Dominio</h3>
 
                        <p>
 
                            Cuentos progresivos con Finx.
                            Comprensión y vocabulario.
 
                        </p>
 
                    </div>
                </div>    
 
                <div class="elemento-etapa">
 
                    <img src="images/finxleyendo.png" class="imagen-finx" alt="Finx">
 
                </div>
 
            </div>
 
        </div>
 
    </section>
 
 
<!-- TESTIMONIOS -->
 
    <section class="testimonios">
 
        <h2>Lo que dicen otros padres</h2>
 
        <div class="testimonios-grid">
<<<<<<< HEAD

    <?php

$resenas=$conn->query("

SELECT

r.*,

u.nombre_papa,
u.nombre_nino,
u.foto_padre

FROM resenas r

JOIN usuarios u

ON r.userID=u.userID

ORDER BY calificacion DESC, fecha DESC

LIMIT 3

");

while($fila=$resenas->fetch_assoc()){

?>

<div class="testimonio">

<div class="estrellas">

<?php

for($i=1;$i<=5;$i++){

if($i<=$fila['calificacion']){

echo '<i class="fa-solid fa-star"></i>';

}

}

?>

</div>

<p>

"<?php echo $fila['comentario']; ?>"

</p>

<div class="persona">

<img

src="images/fotopadre/<?php echo $fila['foto_padre']; ?>"

alt="Foto">

<div>

<h4>

<?php echo $fila['nombre_papa']; ?>

</h4>

<span>

Papá/Mamá de

<?php echo $fila['nombre_nino']; ?>

</span>

</div>

</div>

</div>

<?php

}

?>

</div>

    </div>

        </section>


=======
 
            <!-- TESTIMONIO 1 -->
 
            <div class="testimonio">
 
                <div class="estrellas">
 
                    <i class="fa-solid fa-star"></i>
 
                    <i class="fa-solid fa-star"></i>
 
                    <i class="fa-solid fa-star"></i>
 
                    <i class="fa-solid fa-star"></i>
 
                    <i class="fa-solid fa-star"></i>
 
                </div>
 
                <p>
 
                    "Mi hija empezó a leer sus primeras palabras en pocas semanas. ¡Le encanta!"
 
                </p>
 
                <div class="persona">
 
                    <img
 
                    src="images/mama1.jpg"
 
                    alt="Ana">
 
                    <div>
 
                        <h4>Ana</h4>
 
                        <span>Mamá de Sofía (7 años)</span>
 
                    </div>
 
                </div>
 
            </div>
 
 
            <!-- TESTIMONIO 2 -->
 
            <div class="testimonio">
 
                <div class="estrellas">
 
                    <i class="fa-solid fa-star"></i>
 
                    <i class="fa-solid fa-star"></i>
 
                    <i class="fa-solid fa-star"></i>
 
                    <i class="fa-solid fa-star"></i>
 
                    <i class="fa-solid fa-star"></i>
 
                </div>
 
                <p>
 
                    "Los reportes me dan tranquilidad porque puedo ver su avance día a día."
 
                </p>
 
                <div class="persona">
 
                    <img
 
                    src="images/papa1.jpg"
 
                    alt="Carlos">
 
                    <div>
 
                        <h4>Carlos</h4>
 
                        <span>Papá de Mateo (6 años)</span>
 
                    </div>
 
                </div>
 
            </div>
 
 
            <!-- TESTIMONIO 3 -->
 
            <div class="testimonio">
 
                <div class="estrellas">
 
                    <i class="fa-solid fa-star"></i>
 
                    <i class="fa-solid fa-star"></i>
 
                    <i class="fa-solid fa-star"></i>
 
                    <i class="fa-solid fa-star"></i>
 
                    <i class="fa-solid fa-star"></i>
 
                </div>
 
                <p>
 
                    "El método es claro y efectivo. Mi hijo está más motivado que nunca."
 
                </p>
 
                <div class="persona">
 
                    <img
 
                    src="images/mama2.jpg"
 
                    alt="Lucía">
 
                    <div>
 
                        <h4>Lucía</h4>
 
                        <span>Mamá de Tomás (8 años)</span>
 
                    </div>
 
                </div>
 
            </div>
 
        </div>
 
    </section>
 
 
>>>>>>> 048ffaf4dedcb2ff172269edc990bfcd0dd99336
<!-- FOOTER CTA -->
 
    <section class="cta-section">
 
        <div class="cta-content">
 
            <h2>
 
                Tu hijo puede aprender a leer.
                <br>
 
                <span>¡Empieza hoy mismo!</span>
 
            </h2>
 
            <p>
 
                Únete a Leo & Friends y acompaña a tu hijo
                en una aventura divertida de aprendizaje.
 
            </p>
 
            <a href="register.html" class="cta-btn">
 
                Crear cuenta gratis
 
                <i class="fa-solid fa-arrow-right"></i>
 
            </a>
 
            <small>
 
                Sin tarjeta de crédito • Cancela cuando quieras
 
            </small>
 
        </div>
 
    </section>
 
    <script>
 
        function reproducirAudio(id){
 
            let audio = document.getElementById(id);
 
            audio.currentTime = 0;
 
            audio.play();
 
        }

    </script>

<<<<<<< HEAD
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
=======
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
>>>>>>> 048ffaf4dedcb2ff172269edc990bfcd0dd99336
</body>
</html>