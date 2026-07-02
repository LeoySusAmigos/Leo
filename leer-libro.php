<?php
session_start();

if (!isset($_SESSION['userID'])) {
    header("Location: register.html");
    exit();
}

include 'php/conexion.php';

$libro_id = intval($_GET['id']);

$sql = "SELECT * FROM libros WHERE libro_id = $libro_id";
$res = mysqli_query($conn, $sql);
$libro = mysqli_fetch_assoc($res);

if (!$libro) {
    header("Location: biblioteca.php");
    exit();
}

$sql_paginas = "SELECT * FROM paginas_libro WHERE libro_id = $libro_id ORDER BY numero_pagina ASC";
$res_paginas = mysqli_query($conn, $sql_paginas);

$paginas_raw = [];
$primera = true;
while ($p = mysqli_fetch_assoc($res_paginas)) {
    $paginas_raw[] = [
        'numero' => $p['numero_pagina'],
        'texto'  => $p['texto_pagina'],
        // Solo la primera página lleva imagen
        'imagen' => $primera ? 'images/cuentos/' . $libro['portada'] : null,
    ];
    $primera = false;
}


$paginas_json = json_encode($paginas_raw, JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_HEX_QUOT);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($libro['titulo']); ?></title>
    <link rel="stylesheet" href="styles/navbar.css">
    <link rel="stylesheet" href="styles/leer-libro.css">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600;700&family=Quicksand:wght@500;700&display=swap" rel="stylesheet">
    <style>
        html, body {
            margin: 0; padding: 0;
            min-height: 100vh;
            background-image: url(images/lectura/Fondo.png);
            background-size: 100% 100%;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

        .custom-navbar  { padding: 5px 20px; }
        .mascota-img    { width: 40px; height: 45px; }
        .menu-link      { font-size: 13px; }
        .logo-container img { width: 80px; }
        .nav-element    { margin: 0 10px; }
        .avatar-circle  { width: 30px; height: 30px; }
        .user-info      { font-size: 12px; }
    </style>
</head>
<body>

<?php include 'components/navbar.php'; ?>

<div id="libro-app"></div>

<script src="js/leer-libro.js"></script>
<script>
    const paginas = <?php echo $paginas_json; ?>;

    const libro = new LibroInteractivo('libro-app', paginas, {
        palabrasPorPagina: 60,
        titulo: <?php echo json_encode($libro['titulo']); ?>,
        onTerminar: function({ mins, segs }) {
            fetch('php/guardar-progreso.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    libro_id: <?php echo $libro_id; ?>,
                    tiempo_segundos: mins * 60 + segs
                })
            });

            // Si ves esto chris es para redirigir a la biblioteca
            alert(`¡Muy bien! Terminaste el cuento en ${mins}m ${segs}s`);
            window.location.href = 'biblioteca.php';
        }
    });
</script>

</body>
</html>