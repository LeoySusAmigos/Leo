<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="configuracion.css">
    <title>Configuración</title>
</head>
<body>
    <div class="container">

        <!-- SIDEBAR -->
        <div class="sidebar">
            <h2 class="logo">Leo & Friends</h2>

            <ul>
                <li class="active">Configuración</li>
                <li>Perfil del niño</li>
                <li>Aprendizaje</li>
                <li>Progreso</li>
                <li>Sonido</li>
            </ul>

            <button class="logout">Cerrar Sesión</button>
        </div>

        <!-- MAIN CONTENT -->
        <div class="main">

            <h1>Configuración</h1>
            <p>Administra la experiencia de aprendizaje</p>

            <!-- PERSONAJES -->
            <div class="characters">
                <img src="Finx.png" alt="">
                <img src="Capy.png" alt="">
                <img src="Leito.png" alt="">
            </div>

            <!-- PERFIL -->
            <div class="card">
                <div class="card-header">
                    <img src="cartelito.png" alt="">
                    <div>
                        <h3>Perfil de usuario</h3>
                        <p>Edita la información personal y la edad de tu hijo</p>
                    </div>
                    <button class="arrow">→</button>
                </div>
            </div>

            <!-- APRENDIZAJE -->
            <div class="card">
                <div class="card-header" onclick="toggleCard(this)">
                    <div>
                        <h3>Aprendizaje</h3>
                        <p>Personaliza la experiencia</p>
                    </div>
                    <button class="arrow">↓</button>
                </div>

                <div class="card-content">
                    <p><strong>Leo:</strong> Lectura</p>
                    <p><strong>Capy:</strong> Gramática</p>
                    <p><strong>Finx:</strong> Oraciones</p>
                </div>
            </div>

            <!-- SONIDO -->
            <div class="card">
                <div class="card-header" onclick="toggleCard(this)">
                    <div>
                        <h3>Sonido</h3>
                        <p>Configura música, efectos y narración</p>
                    </div>
                    <button class="arrow">↓</button>
                </div>

                <div class="card-content">
                    <label>Volumen</label>
                    <input type="range" min="0" max="100" value="70">
                </div>
            </div>

        </div>

    </div>
    <script src="js/configuracion.js"></script>
</body>
</html>