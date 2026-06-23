<?php

session_start();

include("conexion.php");

if(!isset($_SESSION['userID'])){

header("Location:../login.html");

exit();

}

$userID=$_SESSION['userID'];

$id=$_GET['id'];

$sql="

SELECT *

FROM resenas

WHERE resenaID='$id'

AND userID='$userID'

";

$resultado=$conn->query($sql);

$resena=$resultado->fetch_assoc();

?>

<!DOCTYPE html>

<html>

<head>

<title>Editar reseña</title>

<link rel="stylesheet"

href="../styles/resenas.css">

</head>

<body>

<div class="editar-box">

<h2>Editar reseña</h2>

<form

action="guardar_resena.php"

method="POST">

<input

type="hidden"

name="editar"

value="<?=$id?>">

<textarea

name="comentario"

required>

<?=$resena['comentario']?>

</textarea>

<select

name="calificacion"

required>

<option value="5">⭐⭐⭐⭐⭐</option>

<option value="4">⭐⭐⭐⭐</option>

<option value="3">⭐⭐⭐</option>

<option value="2">⭐⭐</option>

<option value="1">⭐</option>

</select>

<button type="submit">

Guardar cambios

</button>

</form>

</div>

</body>

</html>