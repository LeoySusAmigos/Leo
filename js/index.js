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

const encabezadosNiveles = document.querySelectorAll(".nivel-row-header");

encabezadosNiveles.forEach(encabezado => {

    encabezado.addEventListener("click", () => {

        const contenedorNivel = encabezado.closest(".nivel-row-container");
        const cajaCuentos = contenedorNivel.querySelector(".nivel-row-cards-flex");
        const flecha = encabezado.querySelector(".toggle-row-icon");

        if (cajaCuentos) {

            if (cajaCuentos.style.display === "none") {

                // Abrir nivel
                cajaCuentos.style.display = "flex";

                if (flecha) {
                    flecha.style.transform = "rotate(0deg)";
                }

            } else {

                // Cerrar nivel
                cajaCuentos.style.display = "none";

                if (flecha) {
                    flecha.style.transform = "rotate(-180deg)";
                }

            }
        }

    });

});