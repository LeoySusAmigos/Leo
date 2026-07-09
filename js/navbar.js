document.addEventListener("DOMContentLoaded", () => {
    
    

    // 2. CONTROL DEL MENÚ RESPONSIVO (HAMBURGUESA)
    const navToggle = document.getElementById("navToggle");
    const mascotasContainer = document.getElementById("mascotas");

    if (navToggle && mascotasContainer) {
        navToggle.addEventListener("click", (event) => {
            event.stopPropagation();
            mascotasContainer.classList.toggle("active");
        });
    }

    // 3. CERRAR MENÚS AL HACER CLIC EN CUALQUIER OTRA PARTE DE LA PANTALLA
    document.addEventListener("click", () => {
        if (dropdownMenu && dropdownMenu.classList.contains("show")) {
            dropdownMenu.classList.remove("show");
        }
        if (mascotasContainer && mascotasContainer.classList.contains("active")) {
            mascotasContainer.classList.remove("active");
        }
    });
});

(function() {

    const imgElement = document.querySelector(".topbar__user .avatar");

    

    if (imgElement) {

            const currentSrc = imgElement.getAttribute('src');

        if (currentSrc) {

            imgElement.src = currentSrc + "?t=" + new Date().getTime();

        }

    }

})();