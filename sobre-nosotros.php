<?php
session_start();
include("php/conexion.php");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sobre Nosotros</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/navbar1.css">
    <link rel="stylesheet" href="styles/index.css">

     <?php include("navbar1.php"); ?>

    <style> 
        /* ── Reset & base ── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Nunito', sans-serif;
            background: #f4f6f4;
            color: #1a202c;
        }

        /* ── Hero ── */
        .hero-about {
            background: linear-gradient(135deg, #1a6e2e 0%, #2d9e4e 60%, #57c84d 100%);
            padding: 65px 24px 65px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .hero-about::before,
        .hero-about::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            background: rgba(255,255,255,.06);
        }

        .hero-about::before {
            width: 300px; height: 300px;
            top: -80px; right: -60px;
        }

        .hero-about::after {
            width: 200px; height: 200px;
            bottom: -80px; left: -40px;
        }

        .hero-about .etiqueta {
            color: rgba(255,255,255,.75);
            font-size: .78rem;
            font-weight: 700;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            margin-bottom: 18px;
        }

        .hero-about h1 {
            font-family: 'Fredoka', sans-serif;
            font-size: 3rem;
            font-weight: 700;
            color: #fff;
            line-height: 1.15;
            margin-bottom: 18px;
            text-shadow: 0 3px 12px rgba(0,0,0,.2);
        }

        .hero-about p {
            color: rgba(255,255,255,.88);
            font-size: 1.1rem;
            max-width: 480px;
            margin: 0 auto;
            line-height: 1.65;
        }

        /* ── Contenido ── */
        .contenido {
            max-width: 960px;
            margin: 0 auto;
            padding: 0 24px 80px;
        }

        /* ── Misión y Visión ── */
        .mv-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 24px;
            margin: 52px 0;
        }

        .mv-card {
            background: #fff;
            border-radius: 20px;
            padding: 48px 40px;
            box-shadow: 0 2px 12px rgba(0,0,0,.06);
            min-height: 280px;
        }

        .mv-card h3 {
            font-family: 'Fredoka', sans-serif;
            font-size: 1.6rem;
            font-weight: 600;
            margin-bottom: 16px;
            color: #1a202c;
        }

        .mv-card p {
            font-size: 1.05rem;
            color: #5a6577;
            line-height: 1.8;
        }

        .mv-icono {
            width: 56px; height: 56px;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 22px;
            font-size: 26px;
        }

        .mv-card.mision { border-top: 4px solid #2d9e4e; }
        .mv-card.vision  { border-top: 4px solid #1e88e5; } 
        .mv-card.planes { border-top: 4px solid #ff7a00; }
        .mv-icono.naranja { background: #fff3e8; color: #cc6200; }

        .mv-icono {
            width: 48px; height: 48px;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 18px;
            font-size: 22px;
        }

        .mv-icono.verde { background: #e8f7ec; color: #1a6e2e; }
        .mv-icono.azul  { background: #e3f2fd; color: #1565c0; }

        .mv-card h3 {
            font-family: 'Fredoka', sans-serif;
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 12px;
            color: #1a202c;
        }

        .mv-card p {
            font-size: .92rem;
            color: #5a6577;
            line-height: 1.75;
        }

        /* ── Cifras ── */
        .cifras {
            background: #1a6e2e;
            border-radius: 24px;
            padding: 44px 32px;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            text-align: center;
            margin-bottom: 56px;
        }

        .cifra-item {
            padding: 0 20px;
        }

        .cifra-item:not(:last-child) {
            border-right: 1px solid rgba(255,255,255,.18);
        }

        .cifra-item .etiqueta {
            color: rgba(255,255,255,.65);
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: 1.8px;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .cifra-item .numero {
            font-family: 'Fredoka', sans-serif;
            font-size: 2.8rem;
            font-weight: 700;
            color: #fff;
            line-height: 1;
        }

        /* ── Sección genérica ── */
        .seccion-label {
            color: #2d9e4e;
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .seccion-titulo {
            font-family: 'Fredoka', sans-serif;
            font-size: 2rem;
            font-weight: 600;
            color: #1a202c;
            margin-bottom: 32px;
        }

        /* ── Metodología ── */
        .metodologia { margin-bottom: 56px; }

        .etapas-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 16px;
        }

        .etapa-card {
            background: #fff;
            border-radius: 18px;
            padding: 26px 22px;
            box-shadow: 0 2px 12px rgba(0,0,0,.06);
            position: relative;
        }

        .etapa-numero {
            font-family: 'Fredoka', sans-serif;
            font-size: 3.5rem;
            font-weight: 700;
            line-height: 1;
            margin-bottom: 14px;
        }

        .etapa-numero.leo  { color: #e8f7ec; }
        .etapa-numero.capy { color: #e3f2fd; }
        .etapa-numero.finx { color: #fff3e8; }

        .etapa-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .etapa-header img {
            width: 36px; height: 36px;
            object-fit: contain;
        }

        .etapa-header h3 {
            font-family: 'Fredoka', sans-serif;
            font-size: 1.05rem;
            font-weight: 600;
            margin: 0;
        }

        .etapa-header h3.leo  { color: #1a6e2e; }
        .etapa-header h3.capy { color: #1565c0; }
        .etapa-header h3.finx { color: #cc6200; }

        .etapa-card p {
            font-size: .88rem;
            color: #5a6577;
            line-height: 1.65;
            margin: 0;
        }

        /* ── Equipo ── */
        .equipo { margin-bottom: 56px; }

        .equipo-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 16px;
        }

        .miembro-card {
            background: #fff;
            border-radius: 16px;
            padding: 0 0 20px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,.06);
            transition: transform .25s ease, box-shadow .25s ease;
            overflow: hidden;
        }

        .miembro-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(0,0,0,.1);
        }

        /* Foto real del miembro */
        .miembro-foto {
            width: 100%;
            aspect-ratio: 1 / 1;
            object-fit: cover;
            object-position: center top;
            display: block;
            margin-bottom: 14px;
        }

        .miembro-card h4 {
            font-size: .88rem;
            font-weight: 700;
            color: #1a202c;
            margin: 0 8px 4px;
            line-height: 1.3;
        }

        .miembro-card span {
            font-size: .75rem;
            color: #7a8594;
        }

        /* ── CTA final ── */
        .cta-final {
            background: #fff;
            border: 1px solid #c8e6c9;
            border-radius: 24px;
            padding: 48px 32px;
            text-align: center;
        }

        .cta-final h2 {
            font-family: 'Fredoka', sans-serif;
            font-size: 1.8rem;
            font-weight: 600;
            color: #1a6e2e;
            margin-bottom: 12px;
        }

        .cta-final p {
            font-size: .95rem;
            color: #5a6577;
            margin-bottom: 28px;
        }

        .cta-botones {
            display: flex;
            gap: 14px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-verde {
            background: #2d9e4e;
            color: #fff;
            padding: 13px 32px;
            border-radius: 14px;
            font-weight: 700;
            font-size: .95rem;
            text-decoration: none;
            transition: background .2s, transform .2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-verde:hover {
            background: #1a6e2e;
            color: #fff;
            transform: translateY(-2px);
        }

        .btn-outline-verde {
            background: transparent;
            color: #2d9e4e;
            padding: 13px 32px;
            border-radius: 14px;
            font-weight: 700;
            font-size: .95rem;
            text-decoration: none;
            border: 2px solid #2d9e4e;
            transition: background .2s, transform .2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-outline-verde:hover {
            background: #e8f7ec;
            color: #1a6e2e;
            transform: translateY(-2px);
        }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .hero-about h1 { font-size: 2.2rem; }
            .mv-grid { grid-template-columns: 1fr; }
            .cifras { grid-template-columns: 1fr; gap: 24px; }
            .cifra-item:not(:last-child) { border-right: none; border-bottom: 1px solid rgba(255,255,255,.18); padding-bottom: 24px; }
            .etapas-grid { grid-template-columns: 1fr; }
            .equipo-grid { grid-template-columns: repeat(3, 1fr); }
        }

        @media (max-width: 425px) {
            .hero-about { padding: 60px 16px 56px; border-radius: 0 0 32px 32px; }
            .hero-about h1 { font-size: 1.8rem; }
            .contenido { padding: 0 16px 60px; }
            .seccion-titulo { font-size: 1.6rem; }
            .equipo-grid { grid-template-columns: repeat(2, 1fr); }
            .cta-botones { flex-direction: column; align-items: center; }
        }

    </style>
</head>
<body>

    <div class="hero-about">
        <p class="etiqueta">Leo & Friends</p>
        <h1>Enseñamos a leer</h1>
        <p>Somos un equipo convencido de que aprender a leer debe ser una aventura, no una tarea.</p>
    </div>


    <div class="contenido">

        <div class="mv-grid">

            <div class="mv-card mision">
                <div class="mv-icono verde">
                    <i class="fa-solid fa-rocket"></i>
                </div>
                <h3>Nuestra misión</h3>
                <p>Empoderar a cada niño en su aprendizaje de la lectura mediante un sistema integral que combina sílabas, gramática y cuentos progresivos, transformando la alfabetización en una aventura divertida y emocionante.</p>
            </div>

            <div class="mv-card vision">
                <div class="mv-icono azul">
                    <i class="fa-solid fa-binoculars"></i>
                </div>
                <h3>Nuestra visión</h3>
                <p>Ser la plataforma líder en alfabetización infantil en español, democratizando el acceso a métodos comprobados para que cada niño descubra el placer de leer y desarrolle habilidades para toda la vida.</p>
            </div>

            <div class="mv-card planes">
                <div class="mv-icono naranja">
                    <i class="fa-solid fa-rocket"></i>
                </div>
                <h3>Planes a futuro</h3>
                <p>Queremos expandir Leo & Friends para incluir a adultos y personas mayores con dificultades en la lectura, adaptando los contenidos a sus necesidades. Además, buscamos hacer la plataforma más interactiva y dinámica, fortaleciendo la sección de Finx con audios, videos, juegos y nuevas actividades.</p>
            </div>

        </div>

        <div class="cifras">

            <div class="cifra-item">
                <p class="etiqueta">Métodos verificados</p>
                <p class="numero">2</p>
            </div>

            <div class="cifra-item">
                <p class="etiqueta">Lecciones creadas</p>
                <p class="numero">+40</p>
            </div>

            <div class="cifra-item">
                <p class="etiqueta">Cuentos disponibles</p>
                <p class="numero">15</p>
            </div>

        </div>



        <div class="equipo">
            <p class="seccion-label">Quiénes somos</p>
            <h2 class="seccion-titulo">El equipo detrás de la aventura</h2>

            <div class="equipo-grid">

                <div class="miembro-card">
                    <img src="images/equipo/valeriaPhoto.jpeg" alt="Valeria Rivas" class="miembro-foto">
                    <h4>Valeria Rivas</h4>
                    <span>Lecciones de Leo</span>
                </div>

                <div class="miembro-card">
                    <img src="images/equipo/gabrielaPhoto.jpeg" alt="Gabriela Bautista" class="miembro-foto">
                    <h4>Gabriela Bautista</h4>
                    <span>Lecciones de Capy</span>
                </div>

                <div class="miembro-card">
                    <img src="images/equipo/chrisPhoto.jpeg" alt="Christopher Valle" class="miembro-foto">
                    <h4>Christopher Valle</h4>
                    <span>Mini biblioteca de Finx</span>
                </div>

                <div class="miembro-card">
                    <img src="images/equipo/danielPhoto.jpeg" alt="Daniel Padilla" class="miembro-foto">
                    <h4>Daniel Padilla</h4>
                    <span>Frontend</span>
                </div>

                <div class="miembro-card">
                    <img src="images/equipo/mauricioPhoto.jpeg" alt="Mauricio López" class="miembro-foto">
                    <h4>Mauricio López</h4>
                    <span>Backend</span>
                </div>

            </div>
        </div>


    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/navbar.js"></script>

</body>
</html>