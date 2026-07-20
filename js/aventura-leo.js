document.addEventListener("DOMContentLoaded", () => {

    const headers = document.querySelectorAll(".nivel-row-header");

    headers.forEach(header => {

        header.addEventListener("click", () => {

            const nivel = header.closest(".nivel-row-container");

            if(nivel.classList.contains("locked-level")){
                return;
            }

            const flecha = nivel.querySelector(".toggle-nivel");

            nivel.classList.toggle("open");

            if(nivel.classList.contains("open")){

                flecha.style.transform = "rotate(180deg)";

            }else{

                flecha.style.transform = "rotate(0deg)";

            }

        });

    });

});