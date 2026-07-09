<?php

$sql="

SELECT

COUNT(*) total,

AVG(calificacion) promedio

FROM resenas

";

$resultado=$conn->query($sql);

$datos=$resultado->fetch_assoc();

$totalResenas=$datos['total'];

$promedio=0;

if($totalResenas>0){

$promedio=

number_format(

$datos['promedio'],

1

);

}

?>