<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - Solicitar Enlace</title>
    <link rel="shortcut icon" href="images/favicon/favicon-32x32.png" type="image/x-icon">
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;700&display=swap" rel="stylesheet">
</head>
<body>
    <main class="contenedorLogin">

        <div class="logoArea">
            <img src="images/cartelito2.png" alt="Leo & Friends">
        </div>

        <div class="login-wrapper">

            <img src="images/Leito.png" class="personaje camaleon" alt="Leo">
            <img src="images/finxhidden.png" class="personaje gato" alt="Finx">
            <img src="images/capyhidden.png" class="personaje capibara" alt="Capy">

            <form class="form" method="POST" action="php/solicitar-enlace.php">
                <div class="header-text">
                    <h1>¿Olvidaste tu contraseña?</h1>
                    <p>Ingresa tu correo electrónico y te enviaremos un enlace para crear una nueva.</p>
                </div>

                <div class="input-container">
                    <i class="fa-solid fa-envelope"></i>
                    <input type="email" class="inputs" placeholder="Correo electrónico" name="correo" required>
                </div>

                <button class="btn-submit" type="submit">Enviar enlace de recuperación</button>

                <p class="signup-text">¿Te acordaste? <a href="login.html"><u>Inicia sesión</u></a></p>
            </form>
        </div>

    </main>
</body>
</html>