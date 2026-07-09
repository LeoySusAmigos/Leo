<?php

session_start();

include("conexion.php");

if(!isset($_SESSION['userID'])){

header("Location:../resenas.php");

exit();

}

$userID=$_SESSION['userID'];

$comentario=$_POST['comentario'];

$calificacion=$_POST['calificacion'];


// Verificar si ya existe una reseña

$sql="

SELECT *

FROM resenas

WHERE userID='$userID'

LIMIT 1

";

$resultado=$conn->query($sql);


// SI YA EXISTE -> EDITAR

if(isset($_POST['editar'])){

$sql="

UPDATE resenas

SET

comentario='$comentario',

calificacion='$calificacion',

fecha_edicion=NOW()

WHERE userID='$userID'

";

$conn->query($sql);

header("Location:../resenas.php");

exit();

}


// SI NO EXISTE -> CREAR

else{

$sql="

INSERT INTO resenas(

userID,

comentario,

calificacion,

fecha

)

VALUES(

'$userID',

'$comentario',

'$calificacion',

NOW()

)

";

}


$conn->query($sql);

header("Location:../resenas.php");

exit();

?>