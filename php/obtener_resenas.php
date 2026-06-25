<?php

$orden="fecha DESC";

if(isset($_GET['orden'])){

if($_GET['orden']=="calificadas"){

$orden="calificacion DESC";

}

elseif($_GET['orden']=="antiguas"){

$orden="fecha ASC";

}

}

$resenas=$conn->query("

SELECT

r.*,

u.nombre_papa,

u.nombre_nino,

u.foto_padre

FROM resenas r

JOIN usuarios u

ON r.userID=u.userID

ORDER BY $orden

");

if($resenas->num_rows==0){

?>

<div class="sin-resenas">

<h3>

Todavía no hay reseñas

</h3>

<p>

Sé el primero en compartir tu experiencia.

</p>

</div>

<?php

}else{

while($fila=$resenas->fetch_assoc()){

?>

<div class="card-resena">

    <div class="info-usuario">

        <img

        src="images/fotopadre/<?php echo $fila['foto_padre']; ?>"

        alt="Foto del padre">

        <div>

            <h3>

                <?php echo $fila['nombre_papa']; ?>

            </h3>

            <span>

                Papá/Mamá de

                <?php echo $fila['nombre_nino']; ?>

            </span>

            <small>

                Publicado el

                <?php

                echo date(

                'd/m/Y',

                strtotime($fila['fecha'])

                );

                ?>

                <?php

                if($fila['fecha_edicion']){

                ?>

                <br>

                ✏️ Editado el

                <?php

                echo date(

                'd/m/Y',

                strtotime($fila['fecha_edicion'])

                );

                }

                ?>

            </small>

        </div>

    </div>


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

    <?php

    if(

    isset($_SESSION['userID'])

    &&

    $_SESSION['userID']==$fila['userID']

    ){

    ?>

    <?php

    }

    ?>

    
</div>

<?php

}

}

?>