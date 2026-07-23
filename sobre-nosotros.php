<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sobre Nosotros</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles/sobre-nosotros.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Balsamiq+Sans:wght@700&family=Fredoka:wght@600;900&family=Nunito:wght@700;900&family=Quicksand:wght@500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/navbar1.css">
</head>

<body>

    <?php include("navbar1.php"); ?>

    <!-- TITULO -->
    <section class="hero">
        <h1>
            ¡Descubre quiénes somos y cómo ayudamos a los niños a aprender!
        </h1>
    </section>

    <!-- MISION Y VISION -->
    <section class="cards-container">

        <!-- MISION -->
        <div class="info-card mision">

            <div class="icon-circle orange">
                <img src="images/sobreNosotros/mision.png" alt="Misión">
            </div>

            <div class="card-content">

                <h2>Nuestra Misión</h2>

                <p>
                    Empoderar a cada niño en su aprendizaje de la lectura mediante un sistema integral que combina vocales, gramática y cuentos progresivos, transformando la alfabetización en una aventura divertida y emocionante.
                </p>

            </div>
        </div>

        <!-- VISION -->
        <div class="info-card vision">

            <div class="icon-circle blue">
                <img src="images/sobreNosotros/vision.png" alt="Visión">
            </div>

            <div class="card-content">

                <h2>Nuestra Visión</h2>

                <p>
                    Ser la plataforma líder en alfabetización infantil en español, democratizando el acceso a métodos comprobados para que cada niño descubra el placer de leer y desarrolle habilidades para toda la vida.
                </p>

            </div>
        </div>

    </section>

    <section class="methodology-section">

        <div class="methodology-card">

            <div class="icon-circle green">
                <img src="images/SobreNosotros/metodologia.png" alt="Metodología">
            </div>

            <div class="metodhology-content">
                <h2>Nuestra Metodología</h2>

                <p class="intro">
                    Nuestro sitio web sigue una ruta de aprendizaje en tres etapas progresivas que garantiza bases sólidas y éxito continuo.
                </p>

                <div class="horizontal-accordion">

                    <!-- ETAPA 1 -->
                    <div class="accordion-card">

                        <button class="accordion-btn">
                            🌟 Etapa 1
                            <span>Fundamentos</span>
                        </button>

                        <div class="accordion-content">

                            <p>
                                El Método de los 20 Días permite que los niños dominen las vocales y combinaciones silábicas mediante actividades auditivas y visuales que fortalecen la decodificación y el reconocimiento de palabras.
                            </p>

                        </div>

                    </div>

                    <!-- ETAPA 2 -->
                    <div class="accordion-card">

                        <button class="accordion-btn">
                            ✏️ Etapa 2
                            <span>Construcción</span>
                        </button>

                        <div class="accordion-content">

                            <p>
                                Los niños utilizan el vocabulario aprendido para formar oraciones completas, comprender estructuras gramaticales y expresar ideas de forma clara y organizada.
                            </p>

                        </div>

                    </div>

                    <!-- ETAPA 3 -->
                    <div class="accordion-card">

                        <button class="accordion-btn">
                            📚 Etapa 3
                            <span>Dominio</span>
                        </button>

                        <div class="accordion-content">

                            <p>
                                A través de cuentos progresivos y ejercicios de comprensión lectora, los estudiantes fortalecen la fluidez, interpretación y análisis de textos.
                            </p>

                        </div>

                    </div>

                </div>
            </div>


        </div>

    </section>

    <script src="js/sobre-nosotros.js"></script>
</body>
</html>