<?php

session_start();

include("php/conexion.php");
include("php/estadisticas_resenas.php");

$diasUso=0;

$lecciones=0;

$puedeResenar=false;

$yaReseno=false;

$editar=false;

if(isset($_SESSION['userID'])){
    if(isset($_GET['editar'])){

    $editar=true;

}

    $userID=$_SESSION['userID'];

    // FECHA DE REGISTRO

    $sql="

    SELECT

    fecha_registro

    FROM usuarios

    WHERE userID=$userID

    ";

    $usuario=$conn->query($sql);

    $usuario=$usuario->fetch_assoc();

    $diasUso=floor(

    (time()-strtotime($usuario['fecha_registro']))

    /86400

    );


    // LECCIONES

    $sql="

    SELECT

    leccion_actual

    FROM progreso

    WHERE userID=$userID

    ";

    $progreso=$conn->query($sql);

    $progreso=$progreso->fetch_assoc();

    $lecciones=$progreso['leccion_actual'] ?? 0;


    $puedeResenar=

    $diasUso>=7

    &&

    $lecciones>=5;


    $sql="

    SELECT *

    FROM resenas

    WHERE userID=$userID

    LIMIT 1

    ";

    $resultado=$conn->query($sql);

    $yaReseno=

    $resultado->num_rows>0;

    $miResena=null;

    if($yaReseno){

    $miResena=$resultado->fetch_assoc();

    }

    

}


?>

<!DOCTYPE html>

<html lang="es">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Reseñas | Leo & Friends</title>
<link rel="shortcut icon" href="images/favicon/favicon-32x32.png" type="image/x-icon">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700&family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="stylesheet" href="styles/navbar1.css">
<link rel="stylesheet" href="styles/resenas.css">

</head>

<body>
    <?php include("navbar1.php"); ?>

<!-- CONTENEDOR PRINCIPAL -->

<div class="contenedor-resenas">


<!-- TITULO -->

<div class="encabezado">

<h1>

<i class="fa-solid fa-seedling"></i>

Reseñas de nuestra comunidad

</h1>

<p>

Conoce la experiencia de otros padres con Leo & Friends

</p>

</div>


<!-- CONTENIDO -->

<div class="contenido">


<!-- LADO IZQUIERDO -->

<div class="lado-izquierdo">


<!-- FILTRO -->

<div class="filtro">

<form method="GET">

<select

    name="orden"

    onchange="this.form.submit()">

    <option value="recientes">

        Más recientes

    </option>

    <option value="calificadas">

        Mejor calificadas

    </option>

    <option value="antiguas">

        Más antiguas

    </option>

</select>

</form>

</div>


<!-- TARJETAS -->

<div class="grid-resenas">

<?php include("php/obtener_resenas.php"); ?>

</div>

</div>


<!-- LADO DERECHO -->

<div class="lado-derecho">


<!-- ESCRIBIR RESEÑA -->

<div class="escribir-resena">

<h2>

<i class="fa-solid fa-pen"></i>

Escribe tu reseña

</h2>

<p>

Comparte tu experiencia.

</p>

<div class="requisitos">

<p>

<?= $diasUso>=7 ? "✅" : "⏳" ?>

Usar la plataforma 7 días o más

</p>

<p>

<?= $lecciones>=5 ? "✅" : "📚" ?>

Completar al menos 5 lecciones

</p>

</div>

<?php

if($yaReseno && !$editar){

?>

<div class="ya-publicada">

<i class="fa-solid fa-circle-check"></i>

<h3>Ya publicaste una reseña</h3>

<p>Puedes editarla o eliminarla.</p>

<div class="acciones-resena">

<a
href="resenas.php?editar=1"
class="btn-editar">

Editar

</a>

<a
href="php/eliminar_resena.php"
class="btn-eliminar"
onclick="return confirm('¿Eliminar tu reseña?')">

Eliminar

</a>

</div>

</div>

<?php

}elseif($yaReseno && $editar){

?>

<div class="mi-resena">

<form
action="php/guardar_resena.php"
method="POST">

<input
type="hidden"
name="editar"
value="1">

<textarea
name="comentario"
required><?= trim($miResena['comentario']) ?></textarea>

<select
name="calificacion"
required>

<option value="5" <?= $miResena['calificacion']==5 ? 'selected' : '' ?>>⭐⭐⭐⭐⭐</option>

<option value="4" <?= $miResena['calificacion']==4 ? 'selected' : '' ?>>⭐⭐⭐⭐</option>

<option value="3" <?= $miResena['calificacion']==3 ? 'selected' : '' ?>>⭐⭐⭐</option>

<option value="2" <?= $miResena['calificacion']==2 ? 'selected' : '' ?>>⭐⭐</option>

<option value="1" <?= $miResena['calificacion']==1 ? 'selected' : '' ?>>⭐</option>

</select>

<div class="botones-mi-resena">

<button
type="submit"
class="btn-guardar">

Guardar cambios

</button>

<a
href="resenas.php"
class="btn-cancelar">

Cancelar

</a>

</div>

</form>

</div>

<?php

}elseif($puedeResenar){

?>

<form
action="php/guardar_resena.php"
method="POST">

<textarea
name="comentario"
required></textarea>

<select
name="calificacion"
required>

<option value="">Selecciona una calificación</option>

<option value="5">⭐⭐⭐⭐⭐</option>

<option value="4">⭐⭐⭐⭐</option>

<option value="3">⭐⭐⭐</option>

<option value="2">⭐⭐</option>

<option value="1">⭐</option>

</select>

<button
type="submit"
class="btn-resena">

Publicar reseña

</button>

</form>

<?php

}
else{

?>

<div class="bloqueado">

<i class="fa-solid fa-lock"></i>

<p>

Debes usar Leo & Friends durante 7 días y completar 5 lecciones.

</p>

</div>

<?php

}

?>

</div>



<!-- ESTADÍSTICAS -->

<div class="estadisticas">

<h2>Calificación promedio</h2>

<h3>

<?php echo $promedio; ?>

</h3>

<div class="estrellas">

<?php

for($i=1;$i<=5;$i++){

if($i<=round($promedio)){

echo "⭐";

}else{

echo "☆";

}

}

?>

</div>

<p>

Basado en

<?php echo $totalResenas; ?>

reseñas

</p>

</div>

</div>

</div>


<!-- BANNER FINAL -->

<div class="banner">

<img src="images/leito.png">

<div>

<h2>¡Únete a Leo & Friends!</h2>

<p>

Miles de niños ya están aprendiendo a leer.

</p>

</div>

<a href="register.html">

Comenzar gratis

</a>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>