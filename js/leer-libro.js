class LibroInteractivo {
  constructor(containerId, paginas, opciones = {}) {
    this.container     = document.getElementById(containerId);
    this.rawPaginas    = paginas;

    console.log(this.rawPaginas);
    console.log(this.rawPaginas[this.rawPaginas.length - 1].texto);

    this.opciones      = Object.assign({ onTerminar: null, palabrasPorPagina: 60 }, opciones);
    this.paginas       = [];
    this.paginaActual  = 0;
    this.timerSegundos = 0;
    this.timerInterval = null;
    this.arrastrando   = false;
    this.dragStartX    = 0;
    this.animando      = false;

    this._dividirPaginas();
    this._render();
    this._iniciarTimer();
  }

  _dividirPaginas() {
    const ppp = this.opciones.palabrasPorPagina;
    this.paginas = [];

    this.rawPaginas.forEach((raw, idx) => {
      const palabras  = raw.texto.trim().split(/\s+/);
      const esPrimera = idx === 0;

      if (esPrimera) {
        const chunk = palabras.splice(0, ppp).join(' ');
        this.paginas.push({ texto: chunk, imagen: raw.imagen || null, numero: raw.numero || (idx + 1) });
      }

      while (palabras.length > 0) {
        const chunk = palabras.splice(0, ppp).join(' ');
        this.paginas.push({ texto: chunk, imagen: null, numero: null });
      }
    });

    this.total = this.paginas.length;
  }

  _render() {
    this.container.innerHTML = `
      <div class="lb-outer">

        <button class="lb-btn-back" id="lb-back" title="Volver">&#8592;</button>

        <div class="lb-wrapper">
          <div class="lb-scene">
            <div class="lb-book" id="lb-book">
              <div class="lb-page-current" id="lb-page-current"></div>
              <div class="lb-page-next"    id="lb-page-next"   ></div>
              <div class="lb-page-flip"    id="lb-page-flip"   ></div>
            </div>

            <div class="lb-controls">
              <button class="lb-nav lb-prev" id="lb-prev" aria-label="Página anterior">&#8592;</button>
              <span class="lb-counter" id="lb-counter"></span>
              <button class="lb-nav lb-next" id="lb-next" aria-label="Página siguiente">&#8594;</button>
            </div>

            <div class="lb-timer">
              <span class="lb-timer-icon">&#9201;</span>
              <span id="lb-timer-display">0:00</span>
            </div>
          </div>
        </div>

      </div>
    `;

    this.elBook    = document.getElementById('lb-book');
    this.elCurrent = document.getElementById('lb-page-current');
    this.elNext    = document.getElementById('lb-page-next');
    this.elFlip    = document.getElementById('lb-page-flip');
    this.elCounter = document.getElementById('lb-counter');
    this.elTimer   = document.getElementById('lb-timer-display');

    document.getElementById('lb-prev').addEventListener('click', () => this._irA(this.paginaActual - 1));
    document.getElementById('lb-next').addEventListener('click', () => this._irA(this.paginaActual + 1));
    document.getElementById('lb-back').addEventListener('click', () => history.back());

    this.elBook.addEventListener('mousedown',  e => this._dragStart(e.clientX));
    this.elBook.addEventListener('touchstart', e => this._dragStart(e.touches[0].clientX), { passive: true });
    window.addEventListener('mouseup',  e => this._dragEnd(e.clientX));
    window.addEventListener('touchend', e => this._dragEnd(e.changedTouches[0].clientX));

    this._mostrarPagina(0, false);
  }

  _contenidoPagina(idx) {
    if (idx < 0 || idx >= this.total) return '';
    const p        = this.paginas[idx];
    const esUltima = idx === this.total - 1;
    const esPrimera = idx === 0;

    let html = '<div class="lb-page-inner">';

    if (esPrimera) {
      html += `
        <div class="lb-header">
          <span class="lb-subtitulo">🌿 Lee y descubre 🌿</span>
          <h2 class="lb-titulo">${this.opciones.titulo || ''}</h2>
        </div>`;
    }

    if (p.imagen) {
      html += `<div class="lb-portada-img"><img src="${p.imagen}" alt="Ilustración del cuento"></div>`;
    }

    const textoLimpio = p.texto.trim();
        const textoEscapado = textoLimpio.replace(/'/g, "\\'").replace(/"/g, '&quot;');
    html += `
      <div class="lb-oraciones">
        <div class="lb-oracion-row">
          <p class="lb-oracion">${textoLimpio}</p>
          <button class="lb-audio" onclick="LibroInteractivo._leer('${textoEscapado}')">
            &#128266;
          </button>
        </div>
      </div>`;
 
    html += `<div class="lb-page-num">${idx + 1} / ${this.total}</div>`;
 
    if (esUltima) {
      html += `
        <div class="lb-listo-wrap">
          <button class="lb-btn-listo" id="lb-listo">&#10003; ¡Listo!</button>
        </div>`;
    }
 
    html += '</div>';
    return html;
  }

  _mostrarPagina(idx, animar = true) {
    if (idx < 0 || idx >= this.total) return;
    if (this.animando) return;

    const siguiente = idx;
    const anterior  = this.paginaActual;
    const avanzando = siguiente >= anterior;

    this.elNext.innerHTML = this._contenidoPagina(siguiente);
    this.elNext.style.zIndex = '1';

    if (!animar) {
        this.elCurrent.innerHTML = this._contenidoPagina(siguiente);
        this.paginaActual = siguiente;
        this._actualizarUI();
        this._bindListo();
        return;
    }

    this.animando = true;

    this.elFlip.innerHTML        = this._contenidoPagina(anterior);
    this.elFlip.style.transition = 'none';
    this.elFlip.style.opacity    = '1';
    this.elFlip.style.zIndex     = '10';
    this.elFlip.style.transform  = avanzando ? 'rotateY(0deg)' : 'rotateY(-180deg)';
    this.elFlip.style.boxShadow  = avanzando
        ? '4px 0 24px rgba(0,0,0,0.18)'
        : '-4px 0 24px rgba(0,0,0,0.18)';

    this.elFlip.getBoundingClientRect();

    this.elFlip.style.transition = 'transform 0.75s cubic-bezier(0.25, 0.46, 0.45, 0.94), box-shadow 0.75s ease';
    this.elFlip.style.transform  = avanzando ? 'rotateY(-180deg)' : 'rotateY(0deg)';
    this.elFlip.style.boxShadow  = '0 0 0 rgba(0,0,0,0)';

    setTimeout(() => {
        this.elFlip.style.transition = '';
        this.elFlip.style.opacity    = '0';
        this.elFlip.style.zIndex     = '-1';
        this.elFlip.style.boxShadow  = '';

        this.elCurrent.innerHTML = this.elNext.innerHTML;

        this.paginaActual = siguiente;
        this.animando = false;
        this._actualizarUI();
        this._bindListo();
    }, 750);
}
  _irA(idx) {
    if (idx < 0 || idx >= this.total) return;
    this._mostrarPagina(idx, true);
  }

  _actualizarUI() {
    const prev = document.getElementById('lb-prev');
    const next = document.getElementById('lb-next');
    if (prev) prev.disabled = this.paginaActual === 0;
    if (next) next.disabled = this.paginaActual === this.total - 1;
    if (this.elCounter) this.elCounter.textContent = `${this.paginaActual + 1} / ${this.total}`;
  }

  _bindListo() {
    const btn = document.getElementById('lb-listo');
    if (btn) {
      btn.addEventListener('click', () => {
        this._detenerTimer();
        const mins = Math.floor(this.timerSegundos / 60);
        const segs = this.timerSegundos % 60;
        if (typeof this.opciones.onTerminar === 'function') {
          this.opciones.onTerminar({ segundos: this.timerSegundos, mins, segs });
        }
      });
    }
  }

  _dragStart(x) { this.arrastrando = true; this.dragStartX = x; }
  _dragEnd(x) {
    if (!this.arrastrando) return;
    this.arrastrando = false;
    const diff = this.dragStartX - x;
    if (Math.abs(diff) > 50) {
      diff > 0 ? this._irA(this.paginaActual + 1) : this._irA(this.paginaActual - 1);
    }
  }

  _iniciarTimer() {
    this.timerInterval = setInterval(() => {
      this.timerSegundos++;
      const m = Math.floor(this.timerSegundos / 60);
      const s = this.timerSegundos % 60;
      if (this.elTimer) this.elTimer.textContent = `${m}:${s.toString().padStart(2,'0')}`;
    }, 1000);
  }

  _detenerTimer() { clearInterval(this.timerInterval); }

  static _leer(texto) {
    const synth = window.speechSynthesis;
    synth.cancel();
    const u = new SpeechSynthesisUtterance(texto);
    u.lang = 'es-ES';
    u.rate = 0.9;
    synth.speak(u);
  }
}

window.LibroInteractivo = LibroInteractivo;