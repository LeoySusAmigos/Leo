const btnEscuchar = document.getElementById("btnEscuchar");

btnEscuchar.addEventListener("click", () => {

    const ruta = btnEscuchar.dataset.audio;

    const audio = new Audio(ruta);

    audio.play();

});