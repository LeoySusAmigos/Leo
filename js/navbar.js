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
<<<<<<< HEAD
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
=======

});
>>>>>>> 11c1e30eaa54335693346f2bf330774fc6d6b640
