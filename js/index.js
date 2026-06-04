const btnFiltro = document.querySelector(".btn-filtro");

const menuFiltro = document.querySelector(".menu-filtro");

const opciones = document.querySelectorAll(".opcion");

const libros = document.querySelectorAll(".card");

/* ABRIR MENU */

btnFiltro.addEventListener("click", () => {

    if(menuFiltro.style.display === "block"){
        menuFiltro.style.display = "none";
    }
    else{
        menuFiltro.style.display = "block";
    }

});

/* FILTRAR */

opciones.forEach(opcion => {

    opcion.addEventListener("click", () => {

        const nivel = opcion.dataset.nivel;

        libros.forEach(libro => {

            if(
                nivel === "todos" ||
                libro.classList.contains(`card-nivel-${nivel}`)
            ){
                libro.style.display = "block";
            }
            else{
                libro.style.display = "none";
            }

        });

        menuFiltro.style.display = "none";

    });

});

const buyButton = document.getElementById("buy-book");
let puntos = document.getElementById("puntos").textContent;

buyButton.onclick = function() {
    if (puntos >= precio_monedas) {
        puntos -= precio_monedas;
        alert("¡Libro comprado!");
    } else {
        alert("No tienes suficientes monedas para comprar este libro.");
    }
};