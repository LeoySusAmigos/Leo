let actual = 0;

const contenedor = document.getElementById("carrusel");
const contenido = document.querySelector(".carrusel-contenido");
const titulo = document.getElementById("carruselTitulo");
const texto = document.getElementById("carruselTexto");
const video = document.getElementById("carruselVideo");
const fuente = document.getElementById("carruselFuente");
const numero = document.getElementById("carruselNumero");
const mascota = document.getElementById("carruselMascota");
const relleno = document.getElementById("carruselRelleno");
const botonesPaso = document.querySelectorAll(".carrusel-paso");

const DURACION_TRANSICION = 280;

function aplicarPaso(indice) {
  actual = (indice + botonesPaso.length) % botonesPaso.length;
  const boton = botonesPaso[actual];

  titulo.innerHTML = boton.dataset.titulo;
  texto.textContent = boton.dataset.texto;
  fuente.src = boton.dataset.video;
  video.load();
  numero.textContent = String(actual + 1).padStart(2, "0");

  mascota.src = boton.dataset.mascota;
  mascota.alt = boton.dataset.mascotaAlt;

  contenedor.className = "carrusel container-" + (actual + 1);

  const porcentaje = (actual / (botonesPaso.length - 1)) * 100;
  relleno.style.width = porcentaje + "%";

  botonesPaso.forEach((btn, i) => {
    btn.classList.toggle("activo", i === actual);
  });
}

function mostrarPaso(indice) {
  if (indice === actual) {
    return;
  }

  contenido.classList.add("carrusel-contenido--transicion");

  setTimeout(() => {
    aplicarPaso(indice);
    contenido.classList.remove("carrusel-contenido--transicion");
  }, DURACION_TRANSICION);
}

document.getElementById("carruselAnterior").addEventListener("click", () => mostrarPaso(actual - 1));
document.getElementById("carruselSiguiente").addEventListener("click", () => mostrarPaso(actual + 1));

botonesPaso.forEach((btn) => {
  btn.addEventListener("click", () => mostrarPaso(Number(btn.dataset.paso)));
});

aplicarPaso(0);