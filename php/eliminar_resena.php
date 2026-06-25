<?php

session_start();

include("conexion.php");

if(!isset($_SESSION['userID'])){

header("Location:../resenas.php");

exit();

}

$userID=$_SESSION['userID'];

$sql="

DELETE

FROM resenas

WHERE userID='$userID'

";

$conn->query($sql);

header("Location:../resenas.php");

exit();

?>