// 1. Seleccionamos los elementos de la interfaz
const btnFiltro = document.querySelector(".btn-filtro-modern");
const menuFiltro = document.querySelector(".menu-filtro");
const opciones = document.querySelectorAll(".opcion");
const filasNiveles = document.querySelectorAll(".nivel-row-container");

/* ==========================================
   AGREGADO: ABRIR Y CERRAR EL MENÚ DE FILTROS
   ========================================== */
if (btnFiltro && menuFiltro) {
    btnFiltro.addEventListener("click", (e) => {
        e.stopPropagation(); // Evita que el clic se propague al documento
        if (menuFiltro.style.display !== "block") {
            menuFiltro.style.display = "block";
        } else {
            menuFiltro.style.display = "none";
        }
    });
}

// Cerrar el menú automáticamente si se hace clic fuera de él
document.addEventListener("click", () => {
    if (menuFiltro && menuFiltro.style.display === "block") {
        menuFiltro.style.display = "none";
    }
});


/* ==========================================
   REPARADO: LÓGICA INTERACTIVA DE FILTRADO
   ========================================== */
opciones.forEach(opcion => {
    opcion.addEventListener("click", () => {
        // Obtenemos el nivel que el usuario seleccionó (ej: "1", "2", "todos")
        const nivelSeleccionado = opcion.getAttribute("data-nivel");

        filasNiveles.forEach(fila => {
            // Buscamos el badge o el número del nivel dentro de esta fila
            // Tu PHP genera un texto interno como "Nivel 1", por lo que extraemos el número final
            const badgeTexto = fila.querySelector(".nivel-badge-pill").textContent;
            const numeroFila = badgeTexto.replace("Nivel ", "").trim();

            // Si seleccionó "todos", mostramos todas las filas
            if (nivelSeleccionado === "todos") {
                fila.style.display = "block";
            } 
            // Si el número coincide, la mostramos. Si no, la ocultamos.
            else if (numeroFila === nivelSeleccionado) {
                fila.style.display = "block";
            } else {
                fila.style.display = "none";
            }
        });

        // Una vez que el niño elige un nivel, cerramos el menú automáticamente
        menuFiltro.style.display = "none";
    });
});

/* 
==========================================================================
   EFECTO ACORDEÓN: ABRIR Y CERRAR NIVELES CON LA FLECHA
========================================================================== 
*/
const flechasNiveles = document.querySelectorAll(".toggle-row-icon");

flechasNiveles.forEach(flecha => {
    flecha.addEventListener("click", () => {
        // Enramos al contenedor padre del nivel actual
        const contenedorNivel = flecha.closest(".nivel-row-container");
        // Buscamos la caja flex que tiene las tarjetas de cuentos dentro de este nivel
        const cajaCuentos = contenedorNivel.querySelector(".nivel-row-cards-flex");

        if (cajaCuentos) {
            // Si las tarjetas están visibles, las escondemos
            if (cajaCuentos.style.display === "none") {
                cajaCuentos.style.display = "flex";
                flecha.style.transform = "rotate(0deg)"; // Flecha apunta arriba
            } else {
                cajaCuentos.style.display = "none";
                flecha.style.transform = "rotate(180deg)"; // Flecha apunta abajo
            }
        }
    });
});
