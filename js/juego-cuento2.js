let letras = (oracionCorrecta || "").split(" ");

letras.sort(() => Math.random() - 0.5);

let container = document.getElementById("container");

let letraArrastrada = null;

let pistasRestantes = 2;

let pistas = [pista, pista2];

function mostrar() {

    container.innerHTML = "";

    letras.forEach((letra, index) => {

        let div = document.createElement("div");

        div.classList.add("letra");
        div.innerText = letra;
        div.setAttribute("draggable", true);
        div.dataset.index = index;

        div.addEventListener("dragstart", () => {
            letraArrastrada = index;
            div.classList.add("dragging");
        });

        div.addEventListener("dragend", () => {
            div.classList.remove("dragging");
        });

        div.addEventListener("dragover", (e) => {
            e.preventDefault();
        });

        div.addEventListener("drop", () => {

            if (letraArrastrada === null) return;

            let temp = letras[letraArrastrada];
            letras[letraArrastrada] = letras[index];
            letras[index] = temp;

            letraArrastrada = null;

            mostrar();
            verificar();
        });

        container.appendChild(div);
    });
}

function verificar() {

    let palabra = letras.join(" ").trim();
    let original = oracionCorrecta.trim();

    if (palabra === original) {

        setTimeout(() => {

            document.getElementById("mensaje").innerHTML = `
                <div class="victoria">
                    <h2>🎉 ¡Ganaste! 🎉</h2>
                    <p>Ordenaste la oración correctamente</p>
                </div>
            `;

        }, 100);
    }
}

function mostrarPista() {

    if (pistasRestantes > 0) {

        pistasRestantes--;

        let pistaRandom = pistas[Math.floor(Math.random() * pistas.length)];

        document.getElementById("mensaje").innerHTML = `
            <div class="victoria">
                <h2>💡 Pista</h2>
                <p>${pistaRandom}</p>
                <small>Pistas restantes: ${pistasRestantes}</small>
            </div>
        `;

        if (pistasRestantes === 0) {
            document.getElementById("botonPista").classList.add("bloqueado");
        }

    } else {

        document.getElementById("mensaje").innerHTML = `
            <div class="victoria">
                <h2>❌ Sin pistas</h2>
                <p>Ya usaste todas las pistas</p>
            </div>
        `;
    }
}

mostrar();