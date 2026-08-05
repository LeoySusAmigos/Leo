<?php
session_start();
if (!isset($_SESSION['userID'])) {
    header('Location: login.html');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Contraseña</title>
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

            <form class="form" method="POST" action="php/cambiar-contrasena.php">
                <div class="header-text">
                    <h1>Cambiar contraseña</h1>
                    <p>Ingresa tu contraseña actual y la nueva contraseña que quieras usar.</p>
                </div>

                <?php if (isset($_GET['status']) && $_GET['status'] === 'error'): ?>
                    <p style="color:#e53e3e;font-weight:700;font-size:.85rem;margin-bottom:12px;">
                        <?php
                        $msg = $_GET['msg'] ?? '';
                        if ($msg === 'actual_incorrecta') echo "La contraseña actual no es correcta.";
                        elseif ($msg === 'no_coinciden') echo "Las contraseñas nuevas no coinciden.";
                        elseif ($msg === 'muy_corta') echo "La nueva contraseña debe tener al menos 6 caracteres.";
                        else echo "Ocurrió un error. Intenta de nuevo.";
                        ?>
                    </p>
                <?php endif; ?>

                <div class="input-container">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" class="inputs" placeholder="Contraseña actual" name="actual" required>
                </div>

                <div class="input-container">
                    <i class="fa-solid fa-key"></i>
                    <input type="password" class="inputs" placeholder="Nueva contraseña" name="nueva" required minlength="6">
                </div>

                <div class="input-container">
                    <i class="fa-solid fa-key"></i>
                    <input type="password" class="inputs" placeholder="Confirmar nueva contraseña" name="confirmar" required minlength="6">
                </div>

                <button class="btn-submit" type="submit">Actualizar contraseña</button>

                <p class="signup-text"><a href="configuracion.php"><u>Volver a Configuración</u></a></p>
            </form>
        </div>

    </main>
</body>
</html>