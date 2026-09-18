/* ============================================================
   progreso.js — Leo & Friends
   Gráfica de evolución, animación de barras y contadores.
   Se carga al final de progreso.php: js/progreso.js
   ============================================================ */

(function () {
    'use strict';

    var sinMovimiento = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    /* ========================================================
       1. Barras de progreso
       ======================================================== */

    function animarBarras() {
        var barras = document.querySelectorAll('.barra-relleno[data-ancho]');

        barras.forEach(function (barra, i) {
            var ancho = Math.max(0, Math.min(100, parseInt(barra.dataset.ancho, 10) || 0));

            if (sinMovimiento) {
                barra.style.width = ancho + '%';
                return;
            }

            setTimeout(function () {
                barra.style.width = ancho + '%';
            }, 120 + i * 90);
        });
    }

    /* ========================================================
       2. Contadores de las métricas
       ======================================================== */

    function animarContadores() {
        var numeros = document.querySelectorAll('[data-contador]');

        numeros.forEach(function (nodo) {
            var destino = parseInt(nodo.dataset.contador, 10) || 0;

            if (sinMovimiento || destino === 0) {
                nodo.textContent = destino;
                return;
            }

            var duracion = 700;
            var inicio = null;

            function paso(tiempo) {
                if (inicio === null) inicio = tiempo;
                var avance = Math.min((tiempo - inicio) / duracion, 1);
                nodo.textContent = Math.round(destino * avance);
                if (avance < 1) requestAnimationFrame(paso);
            }

            requestAnimationFrame(paso);
        });
    }

    /* ========================================================
       3. Gráfica de evolución (SVG, sin librerías)
       ======================================================== */

    var NS = 'http://www.w3.org/2000/svg';

    var ANCHO = 560;
    var ALTO = 260;
    var M = { arriba: 24, derecha: 24, abajo: 42, izquierda: 46 };
    var w = ANCHO - M.izquierda - M.derecha;
    var h = ALTO - M.arriba - M.abajo;

    var ETIQUETAS = ['Semana 1', 'Semana 2', 'Semana 3', 'Semana 4'];

    function crear(tipo, attrs, texto) {
        var el = document.createElementNS(NS, tipo);
        for (var k in attrs) el.setAttribute(k, attrs[k]);
        if (texto !== undefined) el.textContent = texto;
        return el;
    }

    function iniciarGrafica() {
        var contenedorDatos = document.getElementById('datos-grafica');
        var svg = document.getElementById('grafica');
        if (!contenedorDatos || !svg) return;

        var series;
        try {
            series = JSON.parse(contenedorDatos.textContent);
        } catch (e) {
            console.error('No se pudieron leer los datos de la gráfica', e);
            return;
        }

        var ejes = document.getElementById('ejes');
        var linea = document.getElementById('linea');
        var area = document.getElementById('area');
        var puntos = document.getElementById('puntos');
        var select = document.getElementById('serieSelect');

        function dibujar(datos, animar) {
            ejes.textContent = '';
            puntos.textContent = '';

            var maximo = Math.max.apply(null, datos.concat([3]));
            var paso = Math.ceil(maximo / 3);
            var tope = paso * 3;

            function x(i) { return M.izquierda + (w / (datos.length - 1)) * i; }
            function y(v) { return M.arriba + h - (v / tope) * h; }

            // Líneas guía y valores del eje Y
            for (var i = 0; i <= 3; i++) {
                var valor = paso * i;
                var py = y(valor);
                ejes.appendChild(crear('line', {
                    x1: M.izquierda, x2: M.izquierda + w, y1: py, y2: py,
                    stroke: '#ecebe5', 'stroke-width': 1
                }));
                ejes.appendChild(crear('text', {
                    x: M.izquierda - 12, y: py + 4, 'text-anchor': 'end', class: 'eje-texto'
                }, valor));
            }

            // Etiquetas del eje X
            datos.forEach(function (_, i) {
                ejes.appendChild(crear('text', {
                    x: x(i), y: ALTO - 12, 'text-anchor': 'middle', class: 'eje-texto'
                }, ETIQUETAS[i]));
            });

            // Línea y área
            var coords = datos.map(function (v, i) { return x(i) + ',' + y(v); });
            linea.setAttribute('points', coords.join(' '));
            area.setAttribute('d',
                'M ' + x(0) + ',' + (M.arriba + h) +
                ' L ' + coords.join(' L ') +
                ' L ' + x(datos.length - 1) + ',' + (M.arriba + h) + ' Z');

            // Trazo animado
            if (animar && !sinMovimiento && linea.getTotalLength) {
                var largo = linea.getTotalLength();
                linea.style.transition = 'none';
                linea.style.strokeDasharray = largo;
                linea.style.strokeDashoffset = largo;
                // fuerza el reflow para que la transición se aplique
                void linea.getBoundingClientRect();
                linea.style.transition = 'stroke-dashoffset 1s ease';
                linea.style.strokeDashoffset = '0';
                area.style.opacity = '0';
                area.style.transition = 'opacity .8s ease .3s';
                requestAnimationFrame(function () { area.style.opacity = '1'; });
            }

            // Puntos y valores
            datos.forEach(function (v, i) {
                puntos.appendChild(crear('circle', {
                    cx: x(i), cy: y(v), r: 5,
                    fill: '#2fa84f', stroke: '#fff', 'stroke-width': 2
                }));
                puntos.appendChild(crear('text', {
                    x: x(i), y: y(v) - 14, 'text-anchor': 'middle', class: 'punto-valor'
                }, v));
            });
        }

        if (select) {
            select.addEventListener('change', function () {
                dibujar(series[select.value] || series.actividades, true);
            });
        }

        dibujar(series.actividades, true);
    }

    /* ========================================================
       4. Carrusel "Tu próximo paso"
       ======================================================== */

    function iniciarCarrusel() {
        var pista = document.getElementById('carruselPista');
        if (!pista) return;

        var slides = Array.prototype.slice.call(pista.querySelectorAll('.carrusel-slide'));
        var puntos = Array.prototype.slice.call(document.querySelectorAll('#carruselPuntos .punto'));
        var flechas = document.querySelectorAll('.carrusel-flecha');

        function activarPunto(indice) {
            puntos.forEach(function (p, i) {
                p.classList.toggle('activo', i === indice);
            });
        }

        function indiceActivo() {
            var i = puntos.findIndex(function (p) { return p.classList.contains('activo'); });
            return i === -1 ? 0 : i;
        }

        function irASlide(indice) {
            indice = Math.max(0, Math.min(slides.length - 1, indice));
            slides[indice].scrollIntoView({
                behavior: sinMovimiento ? 'auto' : 'smooth',
                inline: 'start',
                block: 'nearest'
            });
            activarPunto(indice);
        }

        puntos.forEach(function (punto, i) {
            punto.addEventListener('click', function () { irASlide(i); });
        });

        flechas.forEach(function (flecha) {
            flecha.addEventListener('click', function () {
                var dir = parseInt(flecha.dataset.dir, 10) || 0;
                irASlide(indiceActivo() + dir);
            });
        });

        // Mantiene los puntos sincronizados si la persona desliza con el dedo
        if ('IntersectionObserver' in window) {
            var observador = new IntersectionObserver(function (entradas) {
                entradas.forEach(function (entrada) {
                    if (entrada.isIntersecting) {
                        var indice = slides.indexOf(entrada.target);
                        if (indice !== -1) activarPunto(indice);
                    }
                });
            }, { root: pista, threshold: 0.6 });

            slides.forEach(function (s) { observador.observe(s); });
        }
    }

    /* ========================================================
       Arranque
       ======================================================== */

    function iniciar() {
        animarBarras();
        animarContadores();
        iniciarGrafica();
        iniciarCarrusel();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', iniciar);
    } else {
        iniciar();
    }
})();