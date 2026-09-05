
document.addEventListener("DOMContentLoaded", () => {
    const btnEscuchar =
    document.getElementById("btnEscuchar");
    const btnContinuar =
    document.getElementById("btnContinuar");

    iniciarLeccion();

    if(btnEscuchar){
        btnEscuchar.addEventListener("click", () => {
            const rutaAudio =
            btnEscuchar.dataset.audio;
            const audio =
            new Audio(rutaAudio);
            btnEscuchar.disabled = true;
            audio.play();
            audio.onended = () => {
                btnContinuar.disabled = false;
                btnEscuchar.disabled = false;
            };
        });
    }
});

const mensajesLeo = {

    fase1: {
        audio: "fase1_indicacion",
        texto: "Escucha atentamente cómo suena esta sílaba. Después la reconoceremos juntos."
    },

    fase2: {
        audio: "fase2_indicacion",
        texto: "¿Cuál de estas es la sílaba correcta?"
    },

    fase3: {
        audio: "fase3_indicacion",
        texto: "Ahora encontremos la palabra completa."
    },

    fase2Correcto:{

        audio:"fase2_correcto",

        textos:[

            "¡Muy bien! Elegiste la sílaba correcta.",

            "¡Lo estás haciendo increíble!",

            "¡Muy bien!",

            "¡Excelente!"

        ]

    },

    fase3Correcto:{

        audio:"fase3_correcto",

        textos:[

            "¡Excelente! Aprendiste una nueva palabra. Ganaste cinco puntos.",

            "¡Sabía que podías! Ganaste cinco puntos.",

            "¡Qué gran trabajo!",

            "¡Increíble! Ganaste cinco puntos"

        ]

    },

    incorrecto:{

        audio:"incorrecto",

        textos:[

            "¡Vamos! Intentémoslo otra vez.",

            "¡Tú puedes!",

            "¡Casi! Volvamos a intentar.",

            "¡No te preocupes! Intentémoslo de nuevo."

        ]

    },

    leccionCompletada:{

        audio:"leccion_completada",

        textos:[

            "¡Lo lograste! Has completado toda la lección. Yo leo con Leo.",

            "¡Completaste toda la lección! Estoy orgulloso de ti. Yo leo con Leo.",

            "¡Terminaste la lección! Yo leo con Leo.",

            "¡Cada vez lees mejor! Yo leo con Leo."

        ]

    }

};


function iniciarLeccion(){

    let indicePendiente = -1;
    let fasePendiente = 1;


    for(let i = 0; i < palabras.length; i++){
        const progreso =
        progresosGuardados.find(
            progreso =>
            Number(progreso.palabraID) ===
            Number(palabras[i].palabraID)
        );

        if(!progreso){
            indicePendiente = i;
            fasePendiente = 1;
            break;
        }

        if(Number(progreso.fase) < 3){
            console.log(
                "Palabra pendiente:",
                palabras[i].palabraID,
                "Fase guardada:",
                progreso.fase
            );
            indicePendiente = i;
            fasePendiente = Number(progreso.fase);
            break;
        }
    }

    if(indicePendiente === -1){
        console.log("Todas las palabras de la lección están completas.");
        return;
    }

    indiceActual = indicePendiente;

    cargarNuevaPalabra();

    if(fasePendiente === 2){
        mostrarFase2();
        return;
    }

    mostrarFase1();
}


function mostrarFase1(){

    document.getElementById("fase1").style.display="flex";

    document.getElementById("fase2").style.display="none";

    document.getElementById("fase3").style.display="none";


    document.querySelectorAll(".progress-step")
    .forEach(step=>{

        step.classList.remove("active");
        step.classList.remove("completed");

    });


    document.getElementById("step1")
    .classList.add("active");


    btnContinuar.disabled=true;


    setTimeout(()=>{

        const audioLeo =
        hablarLeoFijo(
            "fase1",
            "dialogoFase1"
        );

        audioLeo.onended=()=>{

            btnEscuchar.disabled=false;

        };

    },500);

}

function mostrarFase2(){

    document.getElementById("fase1").style.display="none";

    document.getElementById("fase2").style.display="block";

    document.getElementById("fase3").style.display="none";


    document.querySelectorAll(".progress-step")
    .forEach(step=>{

        step.classList.remove("active");
        step.classList.remove("completed");

    });


    document.getElementById("step1")
    .classList.add("completed");

    document.getElementById("step2")
    .classList.add("active");


    cargarFase2();


    hablarLeoFijo(
        "fase2",
        "dialogoFase2"
    );

}

function mostrarFase3(){

    document.getElementById("fase1").style.display="none";

    document.getElementById("fase2").style.display="none";

    document.getElementById("fase3").style.display="block";


    document.querySelectorAll(".progress-step")
    .forEach(step=>{

        step.classList.remove("active");
        step.classList.remove("completed");

    });


    document.getElementById("step1")
    .classList.add("completed");

    document.getElementById("step2")
    .classList.add("completed");

    document.getElementById("step3")
    .classList.add("active");


    cargarFase3();


    hablarLeoFijo(
        "fase3",
        "dialogoFase3"
    );

}


function obtenerNumeroAleatorio(max){

    return Math.floor(Math.random()*max);

}

function hablarLeoFijo(nombre,idBurbuja){

    document.getElementById(idBurbuja).innerHTML =
    mensajesLeo[nombre].texto;

    return reproducirLeo(
        mensajesLeo[nombre].audio
    );

}

function hablarLeo(tipo,idBurbuja){

    const indice =
    Math.floor(Math.random()*mensajesLeo[tipo].textos.length);

    document.getElementById(idBurbuja).innerHTML =
    mensajesLeo[tipo].textos[indice];

    return reproducirLeo(

        mensajesLeo[tipo].audio + (indice+1)

    );

}


async function guardarProgreso(fase){
    const respuesta = await fetch("php/guardarProgreso-leo.php",{
        method:"POST",
        headers:{
            "Content-Type":"application/json"
        },

        body:JSON.stringify({
            nivelID:nivelID,
            leccionID:leccionID,
            palabraID:palabras[indiceActual].palabraID,
            fase:fase
        })
    });

    return await respuesta.json();
}

const fase1 = document.getElementById("fase1");
const fase2 = document.getElementById("fase2");
const fase3 = document.getElementById("fase3");

btnContinuar.addEventListener("click", async () => {

    btnContinuar.disabled = true;
    await guardarProgreso(1);
    fase1.style.display = "none";
    fase2.style.display = "block";


    document.getElementById("step1").classList.remove("active");
    document.getElementById("step1").classList.add("completed");
    document.getElementById("step2").classList.add("active");


    cargarFase2();

    guardarProgreso(2).catch(error => {
        console.log(
            "No fue posible guardar la Fase 2.",
            error
        );
    });

    setTimeout(()=>{
        reproducirLeo(mensajesLeo.fase2.audio);
    },150);

});

function mezclar(array){

    return [...array].sort(() => Math.random() - 0.5);

}

function cargarFase2(){

    const correcta = palabras[indiceActual].silaba;

    let opciones = [correcta];

    while(opciones.length < 3){

        const aleatoria =
        palabras[Math.floor(Math.random()*palabras.length)].silaba;

        if(!opciones.includes(aleatoria)){

            opciones.push(aleatoria);

        }

    }

    opciones = mezclar(opciones);

    const contenedor =
    document.getElementById("opcionesSilabas");

    contenedor.innerHTML="";

    opciones.forEach(silaba=>{

        const boton =
        document.createElement("button");

        boton.className="btn-silaba";

        boton.textContent=silaba;

        boton.onclick=()=>validarSilaba(boton,silaba);

        contenedor.appendChild(boton);

    });

}

function validarSilaba(boton,silaba){

    const correcta =
    palabras[indiceActual].silaba;

    if(silaba===correcta){

        boton.classList.add("correcta");

        const audioLeo =
        hablarLeo("fase2Correcto","dialogoFase2");

        audioLeo.onended = ()=>{

            setTimeout(()=>{

                document.getElementById("fase2").style.display="none";

                document.getElementById("step2").classList.remove("active");
                document.getElementById("step2").classList.add("completed");

                document.getElementById("step3").classList.add("active");

                document.getElementById("fase3").style.display="block";

                cargarFase3();

                setTimeout(()=>{

                     reproducirLeo(mensajesLeo.fase3.audio);

                },150);

            },500);

        };

    }

    else{

        boton.classList.add("incorrecta");

        hablarLeo("incorrecto","dialogoFase2");

        setTimeout(()=>{

            boton.classList.remove("incorrecta");

            document.getElementById("dialogoFase2").innerHTML =
            "¿Cuál de estas es la sílaba correcta?";

        },950);

    }

}

function cargarFase3(){

    const correcta = palabras[indiceActual];

    let opciones = [correcta];

    while(opciones.length < 3){

        const aleatoria =
        palabras[Math.floor(Math.random()*palabras.length)];

        if(!opciones.find(p=>p.palabraID===aleatoria.palabraID)){

            opciones.push(aleatoria);

        }

    }

    opciones = mezclar(opciones);

    const contenedor =
    document.getElementById("opcionesPalabras");

    contenedor.innerHTML="";

    opciones.forEach(opcion=>{

        const card =
        document.createElement("div");

        card.className="card-palabra";

        card.innerHTML=`

            <img src="images/palabrasLeo/${opcion.imagen}">

            <h3>${opcion.palabra}</h3>

        `;

        card.onclick=()=>validarPalabra(card,opcion);

        contenedor.appendChild(card);

    });

}

function validarPalabra(card, opcion){

    if(opcion.palabraID===palabras[indiceActual].palabraID){

        card.classList.add("correcta");

        document.getElementById("step3").classList.remove("active");
        document.getElementById("step3").classList.add("completed");

        const audioLeo =
        hablarLeo("fase3Correcto","dialogoFase3");

        audioLeo.onended = ()=>{

            guardarProgreso(3).then(()=>{

                document.getElementById("opcionesPalabras").style.display="none";

                document.getElementById("resultadoFinal").style.display="flex";

                document.getElementById("contenedorSiguiente").style.display="flex";

            });

        };

    }

    else{

        card.classList.add("incorrecta");

        hablarLeo("incorrecto","dialogoFase3");

        setTimeout(()=>{

            card.classList.remove("incorrecta");

            document.getElementById("dialogoFase3").innerHTML =
            mensajesLeo.fase3.texto;

        },950);

    }

}

const btnSiguientePalabra =
document.getElementById("btnSiguientePalabra");

btnSiguientePalabra.addEventListener("click",()=>{

    indiceActual++;

    if(indiceActual>=palabras.length){

        window.location="aventura-leo.php";

        return;

    }

    cargarNuevaPalabra();

});

function cargarNuevaPalabra(){

    document.getElementById("silabaTexto").textContent =
    palabras[indiceActual].silaba;

    document.getElementById("imagenPrincipal").src =
    "images/palabrasLeo/" +
    palabras[indiceActual].imagen;

    btnEscuchar.dataset.audio =
    "audios/LEO/" +
    palabras[indiceActual].audio;

    btnContinuar.disabled=true;

    document.getElementById("contenedorSiguiente").style.display="none";
    document.getElementById("resultadoFinal").style.display="none";
    document.getElementById("opcionesPalabras").style.display="";
    document.getElementById("dialogoFase3").innerHTML =
    "¡Muy bien! Ahora encontremos la palabra completa.";
    document.getElementById("fase3").style.display="none";
    document.getElementById("fase1").style.display="flex";
    document.getElementById("numeroPalabra").textContent =
    indiceActual+1;

    document.querySelectorAll(".progress-step").forEach(step=>{

        step.classList.remove("active");
        step.classList.remove("completed");

    });

    document.getElementById("step1").classList.add("active");

}

/*=========================================
=          VOLVER AL MAPA
=========================================*/

const btnVolverMapa =
document.getElementById("btnVolverNiveles");

const modalSalir =
document.getElementById("modalSalir");

const cancelarSalir =
document.getElementById("cancelarSalir");

const confirmarSalir =
document.getElementById("confirmarSalir");


btnVolverMapa.onclick = (e)=>{

    e.preventDefault();

    modalSalir.style.display="flex";

};


cancelarSalir.onclick=()=>{

    modalSalir.style.display="none";

};


confirmarSalir.onclick=()=>{

    window.location="aventura-leo.php";

};


modalSalir.onclick=(e)=>{

    if(e.target===modalSalir){

        modalSalir.style.display="none";

    }

};