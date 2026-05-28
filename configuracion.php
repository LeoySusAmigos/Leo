<?php
session_start();

$conexion = new mysqli("localhost", "root", "", "leo_and_friends");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Verificar si existe la sesión
if (isset($_SESSION['userID'])) {

    $userID = $_SESSION['userID'];

    $sql = "SELECT nombre_nino, correo FROM usuarios WHERE userID = $userID";
    $resultado = $conexion->query($sql);

    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();
        $nombre = $usuario['nombre_nino'];
        $correo = $usuario['correo'];
    } else {
        $nombre = "Usuario no encontrado";
        $correo = "";
    }

} else {
    $nombre = "No hay sesión";
    $correo = "";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/configuracion.css">
    <title>Configuración</title>
</head>
<body>
    <div class="container">

        <!-- SIDEBAR -->
        <div class="sidebar">
            <img src="" alt="">

            <ul>
                <li onclick="irProgreso()">Progreso</li>
            </ul>

            <button class="logout">Cerrar Sesión</button>
        </div>

        <!-- MAIN CONTENT -->
        <div class="main">

            <h1>Configuración</h1>
            <p>Administra la experiencia de aprendizaje</p>

            <!-- PERSONAJES -->
            <div class="characters">
                <img src="images/Finx.png" alt="">
                <img src="images/Capy.png" alt="">
                <img src="images/Leito.png" alt="">
            </div>

            <!-- PERFIL -->
            <div class="card">
                <div class="card-header" onclick="toggleCard(this)">
                    
                    <div>
                        <h3>Perfil de usuario</h3>
                        <p>Edita la información personal y la edad de tu hijo</p>
                    </div>

                    <button class="arrow">↓</button>
                </div>

                <div class="card-content">
                    <p><strong>Nombre:</strong> <?php echo $nombre; ?></p>
                    <p><strong>Correo:</strong> <?php echo $correo; ?></p>
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