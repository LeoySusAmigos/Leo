

const COLS    = 4;
const ROWS    = 4;
const API_URL = 'php/ordenar-puzzle.php';
const TAB     = 0.30;   // tamaño orejita como fracción del lado de celda

let pieces    = [];   // { canvas, correctRow, correctCol, pad }
let positions = [];
let solving   = false;
let dragSrc   = null;
let touchSrc  = null;

// ── Cargar libros ──────────────────────────────────────────────
async function cargarLibros() {
  try {
    const res    = await fetch(API_URL);
    const libros = await res.json();
    const grid   = document.getElementById('gridLibros');
    grid.innerHTML = '';

    if (libros.error) { grid.innerHTML = `<div class="loading">Error: ${libros.error}</div>`; return; }
    if (!libros.length) { grid.innerHTML = '<div class="loading">No hay libros disponibles.</div>'; return; }

    libros.forEach(libro => {
      const card = document.createElement('div');
      card.className = 'libro-card';

      const img = document.createElement('img');
      img.alt   = libro.titulo;
      img.onerror = () => { img.src = 'https://placehold.co/200x180?text=Sin+imagen'; };
      img.src   = libro.portada_url;

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

// ── Iniciar juego ──────────────────────────────────────────────
function iniciarJuego(urlPortada, titulo) {
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
    document.getElementById('info').textContent = 'Arrastrá las piezas para armar la imagen. Si no podés, pedí ayuda 😊';
  };
  img.onerror = () => alert('No se pudo cargar la imagen: ' + urlPortada);
  img.src = urlPortada;
}

// ── Trazar forma jigsaw ────────────────────────────────────────
// tabs: { top, right, bottom, left }  1=orejita afuera  -1=adentro  0=liso
function trazarPieza(ctx, x0, y0, W, H, tabs) {
  const t = TAB;
  ctx.beginPath();
  ctx.moveTo(x0, y0);

  // SUPERIOR
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

  // DERECHO
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

  // INFERIOR  (de derecha a izquierda)
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

  // IZQUIERDO  (de abajo a arriba)
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

// ── Cortar imagen ──────────────────────────────────────────────
function cortarEnPiezas(img) {
  const BOARD_W = 400;
  const BOARD_H = Math.round(BOARD_W * img.height / img.width);
  const cellW   = Math.floor(BOARD_W / COLS);
  const cellH   = Math.floor(BOARD_H / ROWS);
  const pad     = Math.round(Math.max(cellW, cellH) * TAB) + 2; // padding para orejitas

  // Tabs internos (borde compartido entre dos piezas)
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

      // Canvas más grande que la celda (incluye área de orejitas vecinas)
      const pw = cellW + 2 * pad;
      const ph = cellH + 2 * pad;

      const canvas = document.createElement('canvas');
      canvas.width  = pw;
      canvas.height = ph;
      const ctx = canvas.getContext('2d');

      // Forma jigsaw: la celda empieza en (pad, pad) dentro del canvas
      trazarPieza(ctx, pad, pad, cellW, cellH, tabs);
      ctx.save();
      ctx.clip();

      // Dibujar porción de imagen (con overlap para las orejitas)
      ctx.drawImage(
        img,
        (c * cellW - pad) * scaleX,
        (r * cellH - pad) * scaleY,
        pw * scaleX,
        ph * scaleY,
        0, 0, pw, ph
      );
      ctx.restore();

      // Borde
      trazarPieza(ctx, pad, pad, cellW, cellH, tabs);
      ctx.strokeStyle = 'rgba(0,0,0,0.4)';
      ctx.lineWidth   = 1.5;
      ctx.stroke();

      result.push({ canvas, correctRow: r, correctCol: c, cellW, cellH, pad });
    }
  }
  return result;
}

// ── Mezclar ────────────────────────────────────────────────────
function mezclar() {
  do {
    for (let i = positions.length - 1; i > 0; i--) {
      const j = Math.floor(Math.random() * (i + 1));
      [positions[i], positions[j]] = [positions[j], positions[i]];
    }
  } while (checkWin());
  renderPuzzle();
  actualizarProgreso();
  document.getElementById('winMsg').style.display = 'none';
  document.getElementById('info').textContent = 'Arrastrá las piezas para armar la imagen. Si no podés, pedí ayuda 😊';
}

// ── Renderizar tablero ─────────────────────────────────────────
function renderPuzzle() {
  const wrap = document.getElementById('stripsWrap');
  wrap.innerHTML = '';

  const { cellW, cellH, pad } = pieces[0];
  const BOARD_W = cellW * COLS;
  const BOARD_H = cellH * ROWS;

  // Contenedor con tamaño exacto del tablero (sin padding extra)
  wrap.style.cssText = `
    position: relative;
    width: ${BOARD_W}px;
    height: ${BOARD_H}px;
    margin: 0 auto;
  `;

  positions.forEach((pieceIdx, cellPos) => {
    const { canvas } = pieces[pieceIdx];
    const row = Math.floor(cellPos / COLS);
    const col = cellPos % COLS;

    const cell = document.createElement('div');
    cell.className       = 'puzzle-cell';
    cell.draggable       = true;
    cell.dataset.cellPos = cellPos;

    // Posicionamos la celda: la esquina top-left de la celda real está en (col*cellW, row*cellH)
    // Pero el canvas tiene padding extra, entonces lo desplazamos negativamente
    cell.style.cssText = `
      position: absolute;
      left: ${col * cellW - pad}px;
      top:  ${row * cellH - pad}px;
      width: ${canvas.width}px;
      height: ${canvas.height}px;
      cursor: grab;
      z-index: 1;
    `;

    const c2 = document.createElement('canvas');
    c2.width  = canvas.width;
    c2.height = canvas.height;
    c2.style.display = 'block';
    c2.getContext('2d').drawImage(canvas, 0, 0);
    cell.appendChild(c2);

    // Número pequeño
    const lbl = document.createElement('span');
    lbl.className   = 'lbl';
    lbl.textContent = cellPos + 1;
    lbl.style.cssText = `
      position: absolute;
      top: ${pad + 2}px;
      left: ${pad + 4}px;
      font-size: 11px;
      font-weight: bold;
      color: #fff;
      text-shadow: 0 0 3px #000;
      pointer-events: none;
      user-select: none;
    `;
    cell.appendChild(lbl);

    // Drag & Drop
    cell.addEventListener('dragstart', e => {
      dragSrc = cellPos;
      setTimeout(() => e.target.style.opacity = '0.5', 0);
      e.dataTransfer.effectAllowed = 'move';
    });
    cell.addEventListener('dragend', e => {
      e.target.style.opacity = '1';
      document.querySelectorAll('.puzzle-cell').forEach(s => s.style.outline = '');
    });
    cell.addEventListener('dragover', e => {
      e.preventDefault();
      document.querySelectorAll('.puzzle-cell').forEach(s => s.style.outline = '');
      e.currentTarget.style.outline = '2px solid #4a90d9';
      e.currentTarget.style.zIndex  = '10';
    });
    cell.addEventListener('dragleave', e => {
      e.currentTarget.style.outline = '';
      e.currentTarget.style.zIndex  = '1';
    });
    cell.addEventListener('drop', e => {
      e.preventDefault();
      e.currentTarget.style.outline = '';
      intercambiarPiezas(dragSrc, parseInt(e.currentTarget.dataset.cellPos));
    });

    // Touch
    cell.addEventListener('touchstart', () => { touchSrc = cellPos; }, { passive: true });
    cell.addEventListener('touchend', e => {
      const t  = e.changedTouches[0];
      const el = document.elementFromPoint(t.clientX, t.clientY);
      const sd = el && el.closest('.puzzle-cell');
      if (sd && touchSrc !== null) intercambiarPiezas(touchSrc, parseInt(sd.dataset.cellPos));
      touchSrc = null;
    });

    wrap.appendChild(cell);
  });
}

// ── Intercambiar piezas ────────────────────────────────────────
function intercambiarPiezas(desde, hasta) {
  if (desde === null || desde === hasta) return;
  [positions[desde], positions[hasta]] = [positions[hasta], positions[desde]];
  renderPuzzle();
  actualizarProgreso();
  if (checkWin()) {
    document.getElementById('winMsg').style.display = 'block';
    document.getElementById('info').textContent = '';
  } else {
    document.getElementById('info').textContent = '¡Bien! Seguí intentando 💪';
  }
}

// ── Check win ──────────────────────────────────────────────────
function checkWin() {
  return positions.every((pieceIdx, cellPos) => {
    const { correctRow, correctCol } = pieces[pieceIdx];
    return correctRow === Math.floor(cellPos / COLS) && correctCol === cellPos % COLS;
  });
}

// ── Progreso ───────────────────────────────────────────────────
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

// ── Botón Ayuda ────────────────────────────────────────────────
document.getElementById('ayudaBtn').addEventListener('click', async () => {
  if (solving) return;
  solving = true;
  ['ayudaBtn', 'mezclarBtn', 'volverBtn'].forEach(id => document.getElementById(id).disabled = true);
  document.getElementById('info').textContent = 'Ordenando poquito a poco... 🤖';

  const total = ROWS * COLS;
  for (let i = 0; i < total; i++) {
    if (positions[i] !== i) {
      const correctPos = positions.indexOf(i);
      [positions[i], positions[correctPos]] = [positions[correctPos], positions[i]];
      renderPuzzle();
      actualizarProgreso();
      await new Promise(r => setTimeout(r, 120));
    }
  }

  solving = false;
  ['ayudaBtn', 'mezclarBtn', 'volverBtn'].forEach(id => document.getElementById(id).disabled = false);
  document.getElementById('winMsg').style.display = 'block';
  document.getElementById('info').textContent     = '';
  actualizarProgreso();
});

document.getElementById('mezclarBtn').addEventListener('click', () => { if (!solving) mezclar(); });
document.getElementById('volverBtn').addEventListener('click', () => {
  if (solving) return;
  document.getElementById('pantallaJuego').style.display  = 'none';
  document.getElementById('pantallaLibros').style.display = 'block';
});

cargarLibros();