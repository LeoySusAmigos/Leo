<?php

session_start();

include("conexion.php");

$userID=$_SESSION['userID'];

$comentario=$_POST['comentario'];

$calificacion=$_POST['calificacion'];

$sql="INSERT INTO resenas(

userID,

comentario,

calificacion

)

VALUES(

'$userID',

'$comentario',

'$calificacion'

)";

$conexion->query($sql);

header("Location:resenas.php");

?>

<div class="lista-resenas">

<?php

$resenas=$conexion->query("

SELECT

r.*,

u.nombre_papa,

u.nombre_nino

FROM resenas r

JOIN usuarios u

ON r.userID=u.userID

ORDER BY fecha DESC

");

while($fila=$resenas->fetch_assoc()){

?>

<div class="card-resena">

<h3>

<?php echo $fila['nombre_papa']; ?>

</h3>

<span>

Papá/Mamá de

<?php echo $fila['nombre_nino']; ?>

</span>

<div class="estrellas">

<?php

for($i=1;$i<=5;$i++){

if($i<=$fila['calificacion']){

echo "⭐";

}

}

?>

</div>

<p>

<?php echo $fila['comentario']; ?>

</p>

</div>

<?php

}

?>

</div>