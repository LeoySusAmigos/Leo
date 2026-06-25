const btnFiltro = document.querySelector(".btn-filtro-modern");
const menuFiltro = document.querySelector(".menu-filtro");
const opciones = document.querySelectorAll(".opcion");
const filasNiveles = document.querySelectorAll(".nivel-row-container");

if (btnFiltro && menuFiltro) {
    btnFiltro.addEventListener("click", (e) => {
        e.stopPropagation();
        if (menuFiltro.style.display !== "block") {
            menuFiltro.style.display = "block";
        } else {
            menuFiltro.style.display = "none";
        }
    });
}

document.addEventListener("click", () => {
    if (menuFiltro && menuFiltro.style.display === "block") {
        menuFiltro.style.display = "none";
    }
});


opciones.forEach(opcion => {
    opcion.addEventListener("click", () => {
        const nivelSeleccionado = opcion.getAttribute("data-nivel");

        filasNiveles.forEach(fila => {
            const badgeTexto = fila.querySelector(".nivel-badge-pill").textContent;
            const numeroFila = badgeTexto.replace("Nivel ", "").trim();

           
            if (nivelSeleccionado === "todos") {
                fila.style.display = "block";
            } 
            
            else if (numeroFila === nivelSeleccionado) {
                fila.style.display = "block";
            } else {
                fila.style.display = "none";
            }
        });

        
        menuFiltro.style.display = "none";
    });
});

const flechasNiveles = document.querySelectorAll(".toggle-row-icon");

flechasNiveles.forEach(flecha => {
    flecha.addEventListener("click", () => {
        const contenedorNivel = flecha.closest(".nivel-row-container");
        const cajaCuentos = contenedorNivel.querySelector(".nivel-row-cards-flex");

        if (cajaCuentos) {
            if (cajaCuentos.style.display === "none") {
                cajaCuentos.style.display = "flex";
                flecha.style.transform = "rotate(0deg)";
            } else {
                cajaCuentos.style.display = "none";
                flecha.style.transform = "rotate(180deg)";
            }
        }
    });
});
