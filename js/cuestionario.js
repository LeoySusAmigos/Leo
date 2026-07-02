let preguntas = [];
let indicePreguntaActual = 0;
const opcionesSeleccionadas = [];

const textoSubtitulo = document.getElementById("texto-subtitulo");
const textoPregunta = document.getElementById("texto-pregunta");
const contenedorOpciones = document.getElementById("contenedor-opciones");

function iniciarCuestionarioBD() {
    textoPregunta.textContent = "Cargando preguntas de Leo & Friends...";
    
    fetch('php/obtener_preguntas.php')
        .then(response => {
            if (!response.ok) {
                throw new Error("No se pudo encontrar el archivo PHP o el servidor falló");
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                textoPregunta.textContent = "Error al cargar el cuestionario.";
                console.error(data.error);
                return;
            }
            
            preguntas = data;
            
            if (preguntas.length > 0) {
                cargarPregunta(); 
            } else {
                textoPregunta.textContent = "No hay preguntas registradas en la base de datos.";
            }
        })
        .catch(error => {
            console.error("Error al conectar con obtener_preguntas.php:", error);
            textoPregunta.textContent = "Error de conexión al cargar preguntas.";
        });
}

function cargarPregunta() {
    const infoPregunta = preguntas[indicePreguntaActual];

    textoSubtitulo.textContent = infoPregunta.subtitulo;
    textoPregunta.textContent = infoPregunta.texto_pregunta;
    contenedorOpciones.innerHTML = "";

    infoPregunta.opciones.forEach(opcion => {
        const boton = document.createElement("button");
        boton.classList.add("boton-opcion");
        boton.textContent = opcion.texto_opcion;


        boton.onclick = () => procesarRespuesta(opcion.opcion_id);

        contenedorOpciones.appendChild(boton);
    });

    actualizarProgreso();
}

function procesarRespuesta(opcionIdSeleccionada) {
    opcionesSeleccionadas.push(opcionIdSeleccionada);

    if (indicePreguntaActual < preguntas.length - 1) {
        indicePreguntaActual++;
        setTimeout(cargarPregunta, 200);
    } else {
        finalizarCuestionario();
    }
}

function actualizarProgreso() {
    for (let i = 1; i <= preguntas.length; i++) {
        const pasoElemento = document.getElementById(`paso-${i}`);
        if (pasoElemento) {
            if (i <= indicePreguntaActual + 1) {
                pasoElemento.classList.add("activo");
            } else {
                pasoElemento.classList.remove("activo");
            }
        }
    }
}

function finalizarCuestionario() {
    textoSubtitulo.textContent = "✨¡Muchas gracias!✨";
    textoPregunta.textContent = "Guardando tus respuestas...";
    contenedorOpciones.innerHTML = "";

    const datosAEnviar = {
        opciones: opcionesSeleccionadas
    };

    fetch('php/guardar_respuestas.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(datosAEnviar)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            textoPregunta.textContent = "¡Preferencias guardadas con éxito!";
            setTimeout(() => {
                window.location.href = 'index.php'; 
            }, 2000);
        } else {
            textoPregunta.textContent = "Hubo un error al guardar: " + data.message;
        }
    })
    .catch(error => {
        console.error("Error en la conexión al guardar:", error);
        textoPregunta.textContent = "Error de red al guardar las respuestas.";
    });
}

iniciarCuestionarioBD();