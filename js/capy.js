document.addEventListener("DOMContentLoaded", () => {
    const niveles = document.querySelectorAll(".capy-level");

    niveles.forEach((nivel) => {

        const encabezado = nivel.querySelector(".level-header");
        const lecciones = nivel.querySelector(".lessons-container");
        const flecha = nivel.querySelector(".level-arrow");


        // Comprobar que existan los elementos
        if (!encabezado || !lecciones || !flecha) {
            return;
        }

        // Todos los niveles empiezan abiertos
        lecciones.style.display = "flex";

        flecha.classList.add("fa-chevron-up");
        flecha.classList.remove("fa-chevron-down");

        encabezado.addEventListener("click", () => {

            const estaAbierto =
                lecciones.style.display !== "none";


            if (estaAbierto) {

                lecciones.style.display = "none";

                flecha.classList.remove("fa-chevron-up");
                flecha.classList.add("fa-chevron-down");

            } else {

                lecciones.style.display = "flex";

                flecha.classList.remove("fa-chevron-down");
                flecha.classList.add("fa-chevron-up");

            }

        });

    });

});