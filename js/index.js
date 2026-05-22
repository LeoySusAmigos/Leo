// document.addEventListener("DOMContentLoaded", () => {
//     const btnFiltrar = document.getElementById("btnFiltrar");
//     const menuNiveles = document.getElementById("menuNiveles");

//     if (!btnFiltrar || !menuNiveles) return;

//     // 1. Mostrar / Ocultar el menú al hacer clic en Filtrar
//     btnFiltrar.addEventListener("click", (e) => {
//         e.stopPropagation(); 
//         menuNiveles.classList.toggle("show");
//     });

//     // Cerrar el menú si hacen clic afuera
//     document.addEventListener("click", () => {
//         menuNiveles.classList.remove("show");
//     });

//     // 2. Filtrar las tarjetas que PHP ya dibujó
//     const opcionesNivel = menuNiveles.querySelectorAll("li");
    
//     opcionesNivel.forEach(opcion => {
//         opcion.addEventListener("click", () => {
//             const nivelSeleccionado = opcion.getAttribute("data-nivel");
            
//             // Cambiar el texto del botón
//             btnFiltrar.innerHTML = nivelSeleccionado === "todos" 
//                 ? "Filtrar ▾" 
//                 : `Nivel ${nivelSeleccionado} ▾`;

//             // Buscar las tarjetas impresas por PHP
//             const tarjetasCuentos = document.querySelectorAll(".tarjeta-cuento"); 

//             tarjetasCuentos.forEach(tarjeta => {
//                 const nivelTarjeta = tarjeta.getAttribute("data-nivel");

//                 if (nivelSeleccionado === "todos" || nivelTarjeta === nivelSeleccionado) {
//                     tarjeta.style.display = "block"; 
//                 } else {
//                     tarjeta.style.display = "none";  
//                 }
//             });
//         });
//     });
// });

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