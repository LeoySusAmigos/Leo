const COLS    = 4;
const ROWS    = 4;
const API_URL = 'php/ordenar-puzzle.php';
const TAB     = 0.30;

let pieces           = [];
let positions        = [];
let ayudasRestantes  = 2;
let solving          = false;
let resuelto         = false;
let dragSrc          = null;
let touchSrc         = null;
let urlPortadaActual = '';

async function cargarLibros() {
  try {
    const res    = await fetch(API_URL);
    const libros = await res.json();
    const grid   = document.getElementById('gridLibros');
    grid.innerHTML = '';

    if (libros.error) { grid.innerHTML = `<div class="loading">Error: ${libros.error}</div>`; return; }
    if (!libros.length) { grid.innerHTML = '<div class="loading">No hay libros disponibles.</div>'; return; }

    const params = new URLSearchParams(window.location.search);
    const libroIdUrl = params.get('libro');

    if (libroIdUrl){
      const libroSeleccionado = libros.find(l => String(l.libro_id) === libroIdUrl);
      if (libroSeleccionado) {
        iniciarJuego(libroSeleccionado.portada_url, libroSeleccionado.titulo);
        return;
      }
    }

    libros.forEach(libro => {
      const card = document.createElement('div');
      card.className = 'libro-card';

      const img = document.createElement('img');
      img.alt     = libro.titulo;
      img.onerror = () => { img.src = 'https://placehold.co/200x 80?text=Sin+imagen'; };
      img.src     = libro.portada_url;

      const tituloDiv = document.createElement('div');
      tituloDiv.className   = 'titulo';
      tituloDiv.textContent = libro.titulo;

      card.appendChild(img);
      card.appendChild(tituloDiv);
      card.addEventListener('click', () => iniciarJuego(libro.portada_url, libro.titulo));
      grid.appendChild(card);
    });
  } catch {
    document.getElementById('gridLibros').innerHTML =
      '<div class="loading">⚠️ Error al cargar libros. ¿Está corriendo XAMPP?</div>';
  }
}

function iniciarJuego(urlPortada, titulo) {
  urlPortadaActual = urlPortada;
  const img = new Image();
  img.crossOrigin = 'anonymous';
  img.onload = () => {
    pieces    = cortarEnPiezas(img);
    positions = Array.from({ length: ROWS * COLS }, (_, i) => i);
    mezclar();
    document.getElementById('tituloLibro').textContent      = '📖 ' + titulo;
    document.getElementById('pantallaLibros').style.display = 'none';
    document.getElementById('pantallaJuego').style.display  = 'block';
    document.getElementById('winMsg').style.display         = 'none';
    document.getElementById('info').textContent = 'Arrastrá las piezas para armar la imagen. Si no podés, pedí ayuda';
  };
  img.onerror = () => alert('No se pudo cargar la imagen: ' + urlPortada);
  img.src = urlPortada;
}

function trazarPieza(ctx, x0, y0, W, H, tabs) {
  const t = TAB;
  ctx.beginPath();
  ctx.moveTo(x0, y0);

  if (tabs.top === 0) {
    ctx.lineTo(x0 + W, y0);
  } else {
    const d = tabs.top;
    ctx.lineTo(x0 + W * 0.37, y0);
    ctx.bezierCurveTo(x0 + W * 0.37, y0 - d * H * t * 0.5,
                      x0 + W * 0.25, y0 - d * H * t,
                      x0 + W * 0.5,  y0 - d * H * t);
    ctx.bezierCurveTo(x0 + W * 0.75, y0 - d * H * t,
                      x0 + W * 0.63, y0 - d * H * t * 0.5,
                      x0 + W * 0.63, y0);
    ctx.lineTo(x0 + W, y0);
  }

  if (tabs.right === 0) {
    ctx.lineTo(x0 + W, y0 + H);
  } else {
    const d = tabs.right;
    ctx.lineTo(x0 + W, y0 + H * 0.37);
    ctx.bezierCurveTo(x0 + W + d * W * t * 0.5, y0 + H * 0.37,
                      x0 + W + d * W * t,        y0 + H * 0.25,
                      x0 + W + d * W * t,        y0 + H * 0.5);
    ctx.bezierCurveTo(x0 + W + d * W * t,        y0 + H * 0.75,
                      x0 + W + d * W * t * 0.5,  y0 + H * 0.63,
                      x0 + W,                     y0 + H * 0.63);
    ctx.lineTo(x0 + W, y0 + H);
  }

  if (tabs.bottom === 0) {
    ctx.lineTo(x0, y0 + H);
  } else {
    const d = tabs.bottom;
    ctx.lineTo(x0 + W * 0.63, y0 + H);
    ctx.bezierCurveTo(x0 + W * 0.63, y0 + H + d * H * t * 0.5,
                      x0 + W * 0.75, y0 + H + d * H * t,
                      x0 + W * 0.5,  y0 + H + d * H * t);
    ctx.bezierCurveTo(x0 + W * 0.25, y0 + H + d * H * t,
                      x0 + W * 0.37, y0 + H + d * H * t * 0.5,
                      x0 + W * 0.37, y0 + H);
    ctx.lineTo(x0, y0 + H);
  }

  if (tabs.left === 0) {
    ctx.lineTo(x0, y0);
  } else {
    const d = tabs.left;
    ctx.lineTo(x0, y0 + H * 0.63);
    ctx.bezierCurveTo(x0 - d * W * t * 0.5, y0 + H * 0.63,
                      x0 - d * W * t,        y0 + H * 0.75,
                      x0 - d * W * t,        y0 + H * 0.5);
    ctx.bezierCurveTo(x0 - d * W * t,        y0 + H * 0.25,
                      x0 - d * W * t * 0.5,  y0 + H * 0.37,
                      x0,                     y0 + H * 0.37);
    ctx.lineTo(x0, y0);
  }

  ctx.closePath();
}

function cortarEnPiezas(img) {
  const BOARD_W = 400;
  const BOARD_H = Math.round(BOARD_W * img.height / img.width);
  const cellW   = Math.floor(BOARD_W / COLS);
  const cellH   = Math.floor(BOARD_H / ROWS);
  const pad     = Math.round(Math.max(cellW, cellH) * TAB) + 2;

  const hTabs = Array.from({ length: ROWS - 1 }, () =>
    Array.from({ length: COLS }, () => (Math.random() < 0.5 ? 1 : -1)));
  const vTabs = Array.from({ length: ROWS }, () =>
    Array.from({ length: COLS - 1 }, () => (Math.random() < 0.5 ? 1 : -1)));

  const result = [];
  const scaleX = img.width  / BOARD_W;
  const scaleY = img.height / BOARD_H;

  for (let r = 0; r < ROWS; r++) {
    for (let c = 0; c < COLS; c++) {
      const tabs = {
        top:    r === 0        ? 0 : -hTabs[r - 1][c],
        bottom: r === ROWS - 1 ? 0 :  hTabs[r][c],
        left:   c === 0        ? 0 : -vTabs[r][c - 1],
        right:  c === COLS - 1 ? 0 :  vTabs[r][c],
      };

      const pw = cellW + 2 * pad;
      const ph = cellH + 2 * pad;

      const canvas = document.createElement('canvas');
      canvas.width  = pw;
      canvas.height = ph;
      const ctx = canvas.getContext('2d');

      trazarPieza(ctx, pad, pad, cellW, cellH, tabs);
      ctx.save();
      ctx.clip();

      ctx.drawImage(
        img,
        (c * cellW - pad) * scaleX,
        (r * cellH - pad) * scaleY,
        pw * scaleX,
        ph * scaleY,
        0, 0, pw, ph
      );
      ctx.restore();

      trazarPieza(ctx, pad, pad, cellW, cellH, tabs);
      ctx.strokeStyle = 'rgba(0,0,0,0.4)';
      ctx.lineWidth   = 1.5;
      ctx.stroke();

      result.push({ canvas, correctRow: r, correctCol: c, cellW, cellH, pad });
    }
  }
  return result;
}

function mezclar() {
  resuelto        = false;
  ayudasRestantes = 2;

  const ayudaBtn = document.getElementById('ayudaBtn');
  ayudaBtn.disabled    = false;
  ayudaBtn.textContent = '¡Ayudame!';

  const imgFinal = document.querySelector('.imagen-final');
  if (imgFinal) imgFinal.remove();

  do {
    for (let i = positions.length - 1; i > 0; i--) {
      const j = Math.floor(Math.random() * (i + 1));
      [positions[i], positions[j]] = [positions[j], positions[i]];
    }
  } while (checkWin());
  renderPuzzle();
  actualizarProgreso();
  document.getElementById('winMsg').style.display = 'none';
  document.getElementById('info').textContent = 'Arrastrá las piezas para armar la imagen. Si no podés, pedí ayuda';
}

function renderPuzzle() {
  const wrap = document.getElementById('stripsWrap');
  wrap.innerHTML = '';

  const { cellW, cellH, pad } = pieces[0];
  const BOARD_W = cellW * COLS;
  const BOARD_H = cellH * ROWS;

  const wrapParent = wrap.parentElement;
  let flexRow = document.getElementById('puzzleFlexRow');
  if (!flexRow) {
    flexRow = document.createElement('div');
    flexRow.id = 'puzzleFlexRow';
    wrapParent.insertBefore(flexRow, wrap);
    flexRow.appendChild(wrap);

    const refBox = document.createElement('div');
    refBox.id = 'refBox';
    refBox.style.cssText = `
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 8px;
      min-width: 300px;
    `;
    const refLabel = document.createElement('div');
    refLabel.textContent = 'Imagen de apoyo';
    refLabel.style.cssText = `
      font-size: 13px;
      font-weight: 700;
      color: #2d4a8a;
      text-align: center;
    `;
    const refImg = document.createElement('img');
    refImg.id = 'refImg';
    refImg.src = urlPortadaActual;
    refImg.style.cssText = `
      width: 300px;
      height: auto;
      border-radius: 10px;
      border: 2px solid #c8d6f5;
      box-shadow: 0 2px 8px rgba(0,0,0,0.12);
      display: block;
    `;
    refBox.appendChild(refLabel);
    refBox.appendChild(refImg);
    flexRow.appendChild(refBox);
  } else {
    document.getElementById('refImg').src = urlPortadaActual;
  }

  const esMobile = window.innerWidth <= 480;
  flexRow.style.cssText = `
    display: flex;
    flex-direction: ${esMobile ? 'column' : 'row'};
    align-items: center;
    justify-content: center;
    gap: ${esMobile ? '20px' : '70px'};
    margin-bottom: 1rem;
  `;

  wrap.style.cssText = `
    position: relative;
    width: ${BOARD_W + pad * 2}px;
    height: ${BOARD_H + pad * 2}px;
    flex-shrink: 0;
  `;

  positions.forEach((pieceIdx, cellPos) => {
    const { canvas } = pieces[pieceIdx];
    const row = Math.floor(cellPos / COLS);
    const col = cellPos % COLS;
    const esCorrecta = pieceIdx === cellPos;

    const cell = document.createElement('div');
    cell.className        = 'puzzle-cell';
    cell.draggable         = !esCorrecta;
    cell.dataset.cellPos   = cellPos;
    cell.dataset.correcta  = esCorrecta ? '1' : '0';

    cell.style.cssText = `
      position: absolute;
      left: ${col * cellW}px;
      top:  ${row * cellH}px;
      width: ${canvas.width}px;
      height: ${canvas.height}px;
      cursor: ${esCorrecta ? 'default' : 'grab'};
      z-index: 1;
    `;

    const c2 = document.createElement('canvas');
    c2.width  = canvas.width;
    c2.height = canvas.height;
    c2.style.display = 'block';
    c2.getContext('2d').drawImage(canvas, 0, 0);
    cell.appendChild(c2);

    const highlight = document.createElement('div');
    highlight.className = 'drop-highlight';
    highlight.style.cssText = `
      position: absolute;
      top: ${pad}px;
      left: ${pad}px;
      width: ${cellW}px;
      height: ${cellH}px;
      border: 2px solid #4a90d9;
      border-radius: 4px;
      pointer-events: none;
      opacity: 0;
      z-index: 10;
    `;
    cell.appendChild(highlight);

    cell.addEventListener('dragstart', e => {
      if (resuelto || cell.dataset.correcta === '1') { e.preventDefault(); return; }

      document.querySelectorAll('.pista-animada').forEach(c => c.classList.remove('pista-animada'));
      cell.style.filter = '';
      dragSrc = cellPos;
      cell.style.outline = '';
      document.querySelectorAll('.drop-highlight').forEach(h => h.style.opacity = '0');
      setTimeout(() => e.target.style.opacity = '0.5', 0);
      e.dataTransfer.effectAllowed = 'move';
    });
    cell.addEventListener('dragend', e => {
      e.target.style.opacity = '1';
      document.querySelectorAll('.drop-highlight').forEach(h => h.style.opacity = '0');
    });
    cell.addEventListener('dragover', e => {
      if (resuelto || cell.dataset.correcta === '1') return;
      e.preventDefault();
      document.querySelectorAll('.drop-highlight').forEach(h => h.style.opacity = '0');
      e.currentTarget.querySelector('.drop-highlight').style.opacity = '1';
    });
    cell.addEventListener('dragleave', e => {
      e.currentTarget.querySelector('.drop-highlight').style.opacity = '0';
    });
    cell.addEventListener('drop', e => {
      if (resuelto || cell.dataset.correcta === '1') return;
      e.preventDefault();
      e.currentTarget.querySelector('.drop-highlight').style.opacity = '0';
      cell.style.outline = '';
      intercambiarPiezas(dragSrc, parseInt(e.currentTarget.dataset.cellPos));
    });

    cell.addEventListener('touchstart', () => {
      if (!resuelto && cell.dataset.correcta !== '1') touchSrc = cellPos;
    }, { passive: true });
    cell.addEventListener('touchend', e => {
      if (resuelto) return;
      const t  = e.changedTouches[0];
      const el = document.elementFromPoint(t.clientX, t.clientY);
      const sd = el && el.closest('.puzzle-cell');
      if (sd && sd.dataset.correcta === '1') { touchSrc = null; return; }
      if (sd && touchSrc !== null) intercambiarPiezas(touchSrc, parseInt(sd.dataset.cellPos));
      touchSrc = null;
    });

    if (resuelto) {
      cell.draggable    = false;
      cell.style.cursor = 'default';
    }

    wrap.appendChild(cell);
  });
}

function intercambiarPiezas(desde, hasta) {
  if (resuelto || desde === null || desde === hasta) return;
  if (positions[desde] === desde || positions[hasta] === hasta) return;
  [positions[desde], positions[hasta]] = [positions[hasta], positions[desde]];
  renderPuzzle();
  actualizarProgreso();
  if (checkWin()) {
    mostrarSolucionado();
  } else {
    document.getElementById('info').textContent = '¡Bien! Seguí intentando';
  }
}

function checkWin() {
  return positions.every((pieceIdx, cellPos) => {
    const { correctRow, correctCol } = pieces[pieceIdx];
    return correctRow === Math.floor(cellPos / COLS) && correctCol === cellPos % COLS;
  });
}

function actualizarProgreso() {
  const total   = ROWS * COLS;
  const correct = positions.filter((pieceIdx, cellPos) => {
    const { correctRow, correctCol } = pieces[pieceIdx];
    return correctRow === Math.floor(cellPos / COLS) && correctCol === cellPos % COLS;
  }).length;
  const pct = Math.round(correct / total * 100);
  document.getElementById('progFill').style.width = pct + '%';
  document.getElementById('progLbl').textContent  = pct + '%';
}

function mostrarSolucionado() {
  resuelto = true;
  document.getElementById('winMsg').style.display = 'block';
  document.getElementById('info').textContent = '';

  document.querySelectorAll('.puzzle-cell').forEach(cell => {
    cell.draggable = false;
    cell.style.cursor  = 'default';
    cell.style.outline = '';
    const h = cell.querySelector('.drop-highlight');
    if (h) h.style.opacity = '0';
  });

  const wrap = document.getElementById('stripsWrap');
  const { cellW, cellH, pad } = pieces[0];
  const BOARD_W = cellW * COLS;
  const BOARD_H = cellH * ROWS;

  const imgFinal = document.createElement('img');
  imgFinal.className = 'imagen-final';
  imgFinal.src = urlPortadaActual;
  imgFinal.style.cssText = `
    position: absolute;
    top: ${pad}px;
    left: ${pad}px;
    width: ${BOARD_W}px;
    height: ${BOARD_H}px;
    object-fit: cover;
    border-radius: 6px;
    opacity: 0;
    transition: opacity 0.6s ease;
    z-index: 20;
    pointer-events: none;
  `;
  wrap.appendChild(imgFinal);

  requestAnimationFrame(() => {
    requestAnimationFrame(() => {
      imgFinal.style.opacity = '1';
    });
  });
}

document.getElementById('ayudaBtn').addEventListener('click', () => {
  if (solving || resuelto || ayudasRestantes <= 0) return;

  const incorrectas = [];

  for (let i = 0; i < positions.length; i++) {
    if (positions[i] !== i) {
      incorrectas.push(i);
    }
  }

  if (!incorrectas.length) return;

  const origen = incorrectas[
    Math.floor(Math.random() * incorrectas.length)
  ];

  ayudasRestantes--;

  document.getElementById('ayudaBtn').textContent =
    `Pista (${ayudasRestantes})`;

  if (ayudasRestantes === 0) {
    document.getElementById('ayudaBtn').disabled = true;
  }

  const cell = document.querySelector(`.puzzle-cell[data-cell-pos="${origen}"]`);
  if (!cell) return;

  const { cellW, cellH } = pieces[0];
  const origRow = Math.floor(origen / COLS);
  const origCol = origen % COLS;
  const destinoCellPos = positions[origen];
  const destRow = Math.floor(destinoCellPos / COLS);
  const destCol = destinoCellPos % COLS;

  const dx = (destCol - origCol) * cellW;
  const dy = (destRow - origRow) * cellH;

  document.querySelectorAll('.puzzle-cell').forEach(c => {
    c.style.zIndex  = '1';
    c.classList.remove('pista-animada');
    c.classList.remove('pista-destino');
  });

  cell.style.zIndex  = '15';
  cell.style.setProperty('--hint-dx', dx + 'px');
  cell.style.setProperty('--hint-dy', dy + 'px');
  cell.classList.add('pista-animada');

  const destinoCell = document.querySelector(`.puzzle-cell[data-cell-pos="${destinoCellPos}"]`);
  if (destinoCell) destinoCell.classList.add('pista-destino');

  document.getElementById('info').textContent = '¡Esa pieza va en otro lugar! Fijate a dónde se movió';
});

document.getElementById('mezclarBtn').addEventListener('click', () => { if (!solving) mezclar(); });

document.getElementById('volverBtn').addEventListener('click', () => {
  if (solving) return;
  window.location.href = 'biblioteca.php';
});

cargarLibros();