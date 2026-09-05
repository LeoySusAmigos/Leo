
const navToggle = document.getElementById("navToggle");
const mascotas = document.getElementById("mascotas");

navToggle.addEventListener("click", () => {
    mascotas.classList.toggle("active");
});

/* ANIMAR BARRAS DE PROGRESO */
document.addEventListener("DOMContentLoaded", () => {
    const barras = document.querySelectorAll("#barraHero, .mini-barra-interna");

    barras.forEach((barra) => {
        const destino = barra.dataset.progreso || 0;

        // pequeño delay para que la transición sea visible al entrar a la página
        requestAnimationFrame(() => {
            setTimeout(() => {
                barra.style.width = destino + "%";
            }, 150);
        });
    });
});
