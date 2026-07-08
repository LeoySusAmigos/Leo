document.addEventListener("DOMContentLoaded", () => {

    const navToggle = document.getElementById("navToggle");
    const mascotasContainer = document.getElementById("mascotas");

    if (navToggle && mascotasContainer) {
        navToggle.addEventListener("click", (event) => {
            event.stopPropagation();
            mascotasContainer.classList.toggle("active");
        });
    }

    if (mascotasContainer) {
        mascotasContainer.addEventListener("click", (event) => {
            event.stopPropagation();
        });
    }

    document.addEventListener("click", () => {
        if (mascotasContainer && mascotasContainer.classList.contains("active")) {
            mascotasContainer.classList.remove("active");
        }
    });

});