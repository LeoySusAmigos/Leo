let letras = oracionCorrecta.split(" ");

letras.sort(() => Math.random() - 0.5);

let container = document.getElementById("container");

let letraArrastrada = null;

function mostrar(){

    container.innerHTML = "";

    letras.forEach((letra, index) => {

        let div = document.createElement("div");

        div.classList.add("letra");

        div.innerText = letra;

        div.setAttribute("draggable", true);

        div.dataset.index = index;

        // Cuando empieza a arrastrar
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

            let temp = letras[letraArrastrada];

            letras[letraArrastrada] = letras[index];

            letras[index] = temp;

            mostrar();

            verificar();
        });

        container.appendChild(div);
    });
}

function verificar(){

    let palabra = letras.join(" ");

    if(palabra === oracionCorrecta){

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
mostrar();