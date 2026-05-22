// Definimos los niveles basados en el cuento de la imagen image_657102.jpg
const niveles = [
    {
        correcta: ["La", "araña", "Ana", "ve", "una", "manzana"],
        pista: "¡Mira lo que encontró Ana!"
    },
    {
        correcta: ["Ana", "ama", "la", "manzana", "y", "la", "agarra"],
        pista: "¡A Ana le gusta mucho su fruta!"
    },
    {
        correcta: ["La", "araña", "Ana", "ahora", "salta", "y", "canta"],
        pista: "¡Ana está muy feliz!"
    }
];

let nivelActual = 0;
let oracionUsuario = [];

const bank = document.getElementById('wordsBank');
const display = document.getElementById('displayArea');

function iniciarNivel() {
    // Limpiamos todo para el nuevo nivel
    bank.innerHTML = '';
    display.innerHTML = '<span class="placeholder">Toca las palabras de abajo...</span>';
    oracionUsuario = [];

    const datosNivel = niveles[nivelActual];
    
    // Desordenamos las palabras
    let palabrasDesordenadas = [...datosNivel.correcta].sort(() => Math.random() - 0.5);

    palabrasDesordenadas.forEach(palabra => {
        const btn = document.createElement('div');
        btn.className = 'word-bubble';
        btn.innerText = palabra;
        btn.onclick = () => seleccionarPalabra(palabra, btn);
        bank.appendChild(btn);
    });
}

function seleccionarPalabra(p, elemento) {
    if (oracionUsuario.length === 0) display.innerHTML = ''; 
    
    oracionUsuario.push(p);
    elemento.style.visibility = 'hidden'; // Usamos visibility para que no se mueva el layout
    
    const span = document.createElement('span');
    span.className = 'word-bubble'; 
    span.style.background = '#8bc34a';
    span.innerText = p;
    display.appendChild(span);
}

function verificarOracion() {
    const correcta = niveles[nivelActual].correcta;
    
    if (JSON.stringify(oracionUsuario) === JSON.stringify(correcta)) {
        
        if (nivelActual < niveles.length - 1) {
            alert("¡Muy bien! Siguiente oración.");
            nivelActual++;
            iniciarNivel();
        } else {
            alert("¡Felicidades! Completaste todo el cuento de Ana.");
            guardarProgresoFinal();
        }
    } else {
        alert("Oh no, el orden no es correcto. ¡Intenta otra vez!");
        iniciarNivel();
    }
}

function guardarProgresoFinal() {
    // Preparamos los datos
    const datos = new FormData();
    datos.append('actividad', 'Ana y la manzana');
    datos.append('puntos', 3); // Por ejemplo, 1 punto por cada oración

    // Enviamos al servidor
    fetch('php/guardar_logro.php', {
        method: 'POST',
        body: datos
    })
    .then(respuesta => respuesta.text())
    .then(mensaje => {
        console.log(mensaje);
        alert("¡Progreso guardado! Volviendo a tu perfil...");
        window.location.href = "profile.php"; // Redirigir al perfil corregido
    })
    .catch(error => console.error('Error:', error));
}

function reinicioJuego() {
    iniciarNivel();
}

// Arrancamos el primer nivel
iniciarNivel();