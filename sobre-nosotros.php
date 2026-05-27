<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sobre Nosotros</title>

    <link rel="stylesheet" href="styles/sobre-nosotros.css">
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>
    <header class="navbar">

        <div class="logo">
            <img src="images/Leo-1.png" alt="Logo">
        </div>

        <nav>
            <ul>
                <li><a href="index.php">Inicio</a></li>
                <li><a href="sobre-nosotros.php" class="active">Sobre Nosotros</a></li>
                <li><a href="juegos.php">Juegos</a></li>
            </ul>
        </nav>

    </header>

    <!-- TITULO -->
    <section class="hero">
        <h1>
            ¡Descubre quiénes somos y cómo ayudamos a los niños a aprender!
        </h1>
    </section>

    <!-- MISION Y VISION -->
    <section class="cards-container">

        <!-- MISION -->
        <div class="info-card mission">

            <div class="icon-circle orange">
                <i class="fa-solid fa-rocket"></i>
            </div>

            <div class="card-content">

                <h2>Nuestra Misión</h2>

                <p>
                    Empoderar a cada niño en su aprendizaje de la lectura mediante un sistema integral que combina vocales, gramática y cuentos progresivos, transformando la alfabetización en una aventura emocionante.
                </p>

            </div>
        </div>

        <!-- VISION -->
        <div class="info-card vision">

            <div class="icon-circle blue">
                <i class="fa-solid fa-telescope"></i>
            </div>

            <div class="card-content">

                <h2>Nuestra Visión</h2>

                <p>
                    Ser la plataforma líder en alfabetización infantil en español, democratizando el acceso a métodos comprobados para que cada niño descubra el placer de leer y desarrolle habilidades para toda la vida.
                </p>

            </div>
        </div>

    </section>

    <!-- METODOLOGIA -->
    <section class="methodology-section">

        <div class="methodology-card">

            <div class="icon-circle green">
                <i class="fa-solid fa-bullseye"></i>
            </div>

            <h2>Nuestra Metodología</h2>

            <p class="intro">
                Nuestro sitio web sigue una ruta de aprendizaje progresiva en tres etapas.
            </p>

            <!-- ACORDEON -->

            <div class="horizontal-accordion">

                    <!-- ETAPA 1 -->
                <div class="accordion-card">
                    <button class="accordion-btn">🌟 Etapa 1</button>

                    <div class="accordion-content">

                        <h3>Fundamentos</h3>

                        <p>
                            El Método de los 20 Días estructurado donde los niños dominan las vocales y las combinaciones silábicas del español con audios.
                        </p>

                </div>

                </div>

                    <!-- ETAPA 2 -->
                <div class="accordion-card">

                    <button class="accordion-btn">✏️ Etapa 2</button>

                    <div class="accordion-content">

                        <h3>Construcción</h3>

                        <p>
                            Gramática y oraciones utilizando el vocabulario aprendido para formar ideas y expresarse correctamente.
                        </p>

                    </div>

                </div>

                    <!-- ETAPA 3 -->
                <div class="accordion-card">

                    <button class="accordion-btn">
                        📚 Etapa 3
                    </button>

                    <div class="accordion-content">

                        <h3>Dominio</h3>

                        <p>
                            Cuentos y comprensión lectora para reforzar el aprendizaje mediante historias progresivas.
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </section>

    <script>

        const cards = document.querySelectorAll(".accordion-card");

        cards.forEach(card => {

        const button = card.querySelector(".accordion-btn");

        button.addEventListener("click", () => {

        // cerrar las demás
        cards.forEach(otherCard => {

            if(otherCard !== card){
                otherCard.classList.remove("active");
            }

        });

        // abrir actual
        card.classList.toggle("active");

        });

        });

    </script>

</body>
</html>