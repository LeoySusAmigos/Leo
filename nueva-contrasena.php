<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Establecer Nueva Contraseña</title>
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

            <form class="form" method="POST" action="php/recuperar-contrasena.php">
                
                <?php 
                    $token = isset($_GET['token']) ? htmlspecialchars($_GET['token']) : ''; 
                ?>
                <input type="hidden" name="token" value="<?php echo $token; ?>">

                <div class="header-text">
                    <h1>Crea tu nueva contraseña</h1>
                    <p>Por seguridad, elige una contraseña que no olvides fácilmente.</p>
                </div>

                <div class="input-container">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" class="inputs" placeholder="Nueva contraseña" name="password" required>
                </div>

                <div class="input-container">
                    <i class="fa-solid fa-circle-check" style="color: #136327;"></i> <input type="password" class="inputs" placeholder="Confirmar nueva contraseña" name="confirm_password" required>
                </div>

                <button class="btn-submit" type="submit">Actualizar contraseña</button>

                <p class="signup-text">¿Te acordaste? <a href="login.html"><u>Inicia sesión</u></a></p>
            </form>
        </div>

    </main>
</body>
</html>