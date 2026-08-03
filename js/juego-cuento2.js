let palabras        = fraseCompleta.trim().split(" ");
let indicePalabra   = palabras.findIndex(p => p.toLowerCase() === oracionCorrecta.toLowerCase());
let palabraOculta   = oracionCorrecta;
let palabraArrastrada = null;
let pistasRestantes = 2;
let pistas          = [pista, pista2];
let pistasUsadas    = [];
let respondido      = false;

function construirFrase() {
    const fraseEl = document.getElementById('frase-container');
    fraseEl.innerHTML = '';

    palabras.forEach((palabra, i) => {
        if (i === indicePalabra) {
            const espacio = document.createElement('span');
            espacio.classList.add('espacio-frase');
            espacio.id = 'espacio-frase';
            fraseEl.appendChild(espacio);
        } else {
            const span = document.createElement('span');
            span.classList.add('palabra-frase');
            span.textContent = palabra;
            fraseEl.appendChild(span);
        }
    });
}

function configurarZonaDrop() {
    const zona = document.getElementById('zona-drop');

    zona.addEventListener('dragover', (e) => {
        e.preventDefault();
        zona.classList.add('drag-over');
    });

    zona.addEventListener('dragleave', () => {
        zona.classList.remove('drag-over');
    });

    zona.addEventListener('drop', (e) => {
        e.preventDefault();
        zona.classList.remove('drag-over');
        if (palabraArrastrada && !respondido) {
            colocarPalabra(palabraArrastrada);
        }
    });

    document.addEventListener('dragover', (e) => {
        if (e.target.id === 'espacio-frase') {
            e.preventDefault();
            e.target.classList.add('drag-over');
        }
    });

    document.addEventListener('drop', (e) => {
        if (e.target.id === 'espacio-frase' && palabraArrastrada && !respondido) {
            e.preventDefault();
            e.target.classList.remove('drag-over');
            colocarPalabra(palabraArrastrada);
        }
    });
}

function colocarPalabra(palabra) {
    respondido = true;

    const espacio    = document.getElementById('espacio-frase');
    const zona       = document.getElementById('zona-drop');
    const esCorrecta = palabra.toLowerCase() === palabraOculta.toLowerCase();

    espacio.textContent = palabra;
    espacio.classList.add(esCorrecta ? 'correcto' : 'incorrecto');

    zona.innerHTML = `<span>${palabra}</span>`;
    zona.classList.add(esCorrecta ? 'correcto' : 'incorrecto');

    document.querySelectorAll('.opcion').forEach(op => {
        op.setAttribute('draggable', false);
        op.classList.add('bloqueado');
    });

    setTimeout(() => {
        if (esCorrecta) {
            document.getElementById('mensaje').innerHTML = `
                <div class="victoria">
                    <h2>Correcto</h2>
                    <p>Completaste la frase correctamente.</p>
                </div>`;
        } else {
            document.getElementById('mensaje').innerHTML = `
                <div class="victoria incorrecto-msg">
                    <h2>Casi</h2>
                    <p>La respuesta correcta era <strong>"${palabraOculta}"</strong>.</p>
                </div>`;
        }
    }, 300);
}

function construirOpciones() {
    const contenedor = document.getElementById('opciones-container');
    contenedor.innerHTML = '';

    let ops = [...opciones].sort(() => Math.random() - 0.5);

    ops.forEach(op => {
        const div = document.createElement('div');
        div.classList.add('opcion');
        div.textContent = op;
        div.setAttribute('draggable', true);

        div.addEventListener('dragstart', () => {
            palabraArrastrada = op;
            div.classList.add('dragging');
        });

        div.addEventListener('dragend', () => {
            div.classList.remove('dragging');
        });

        div.addEventListener('touchstart', () => {
            palabraArrastrada = op;
        }, { passive: true });

        contenedor.appendChild(div);
    });
}

function mostrarPista() {
    if (pistasRestantes > 0) {

        let pistasDisponibles = pistas
            .map((p, i) => ({ texto: p, indice: i }))
            .filter(p => !pistasUsadas.includes(p.indice));

        let elegida = pistasDisponibles[Math.floor(Math.random() * pistasDisponibles.length)];
        pistasUsadas.push(elegida.indice);
        pistasRestantes--;

        document.getElementById('mensaje').innerHTML = `
            <div class="victoria">
                <h2>Pista</h2>
                <p>${elegida.texto}</p>
                <small>Pistas restantes: ${pistasRestantes}</small>
            </div>`;

        if (pistasRestantes === 0) {
            document.getElementById('botonPista').classList.add('bloqueado');
        }

    } else {
        document.getElementById('mensaje').innerHTML = `
            <div class="victoria">
                <h2>Sin pistas</h2>
                <p>Ya usaste todas las pistas</p>
            </div>`;
    }
}

construirFrase();
configurarZonaDrop();
construirOpciones();