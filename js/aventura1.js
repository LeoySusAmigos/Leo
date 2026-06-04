const niveles = document.querySelectorAll('.nivel');

niveles.forEach(nivel => {

    nivel.addEventListener('click', ()=>{

        alert('¡Bienvenido al nivel!');

    });

});