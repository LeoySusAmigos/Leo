// ============================================================
//  SCRIPT.JS - Laberinto de Ana
// ============================================================

// --- GRID DEL LABERINTO (60x42, 0=camino, 1=pared) ---
const MAZE_GRID = [[1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1],[1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1],[1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1],[1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1],[1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1],[1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1],[1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1],[1,1,1,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,1,1,1,1,1,1,1,1,1],[1,1,1,0,1,1,1,1,1,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,1,1,1,1,1,1,1,1,1],[1,1,1,1,1,1,1,1,1,1,0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0,0,0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0,0,1,1,1,1,1,1,1,1,1,1],[1,1,1,0,0,0,0,0,0,0,0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0,0,0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0,0,1,1,1,1,1,1,1,1,1,1],[1,1,1,1,0,0,0,0,0,0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0,0,0,1,1,1,1,1,1,1,1,1,0,0,1,1,1,1,0,0,1,1,1,1,1,1,1,1,1,1],[1,1,1,1,1,1,0,0,1,1,1,1,0,1,1,1,1,1,1,1,1,1,1,0,1,1,1,1,1,1,0,0,0,1,1,1,1,1,1,1,1,1,0,1,1,1,1,1,0,0,1,1,1,1,1,1,1,1,1,1],[1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0,0,1,1,1,1,1,0,0,0,0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0,0,0,1,1,1,1,1,1,1,1,1,1],[1,1,1,1,1,1,1,0,0,0,1,1,1,1,1,1,1,1,0,1,1,1,1,1,1,0,0,0,0,0,0,0,0,1,1,1,1,1,1,1,1,1,1,0,0,0,0,0,0,0,1,1,1,1,1,1,1,1,1,1],[1,1,1,1,1,1,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,1,1,0,0,0,1,1,1,0,0,0,1,1,1,1,1,1,1,1,1,1,0,0,1,1,0,0,1,1,1,1,1,1,1,1,1,1,1],[1,1,1,0,0,1,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,1,1,0,0,0,1,1,1,1,0,0,0,0,0,0,0,1,1,1,1,1,0,0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1],[1,1,1,0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0,0,1,1,1,0,0,0,1,1,1,1,0,0,0,0,0,0,0,0,1,1,1,1,0,0,1,1,1,1,1,1,1,0,1,1,1,1,1,1,1],[1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0,0,1,1,1,0,0,0,1,1,1,1,1,1,1,1,1,0,0,0,1,1,1,1,0,0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1],[1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0,0,1,1,1,0,0,0,1,1,1,1,1,1,1,1,1,0,0,0,1,1,1,1,0,0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1],[1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0,0,0,0,0,0,0,0,1,1,1,1,1,1,1,1,1,0,0,0,1,1,1,0,0,0,0,1,1,1,1,1,1,1,1,1,1,1,1,1,1],[1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0,0,0,0,0,0,0,0,1,1,1,1,1,1,1,1,1,0,0,0,1,1,1,0,0,0,0,0,0,0,0,0,0,1,1,0,0,0,1,1,1],[1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0,0,0,1,1,1,0,1,1,1,1,1,1,1,1,1,1,1,0,0,0,1,1,1,1,1,0,0,0,0,0,0,0,0,1,1,1,1,1,1,1,1],[1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0,0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0,0,0,1,1,1,1,1,1,1,1,1,1,1,0,0,1,1,1,1,1,1,1,1],[1,1,1,1,1,1,1,0,0,0,0,0,0,0,0,0,0,0,0,0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0,0,0,1,1,1,1,1,1,1,1,1,1,1,0,0,0,1,1,1,1,1,1,1],[1,1,1,1,1,1,1,0,0,0,0,0,0,0,0,0,0,0,0,1,1,1,1,1,0,0,1,1,1,1,1,1,1,1,1,1,0,0,0,1,1,1,1,1,1,1,1,1,1,1,0,0,0,1,1,1,1,1,1,1],[1,1,1,1,1,1,1,0,0,0,0,0,1,1,0,0,0,1,1,1,0,0,0,0,0,1,1,1,1,1,1,1,1,1,1,1,0,0,0,1,1,1,1,1,1,1,1,1,1,1,0,0,0,1,1,1,1,1,1,1],[1,1,1,1,1,1,1,0,0,1,1,1,1,1,1,1,1,1,1,1,0,0,0,1,1,1,1,1,1,1,0,0,0,0,0,0,0,0,0,0,1,1,1,1,1,1,1,1,1,1,0,0,0,1,1,1,1,1,1,1],[1,1,1,1,1,1,1,0,0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0,1,1,1,1,1,0,0,0,1,1,1,1,1,1,1],[1,1,1,1,1,1,1,0,0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0,0,0,1,1,1,1,1,0,0,0,0,0,0,1,1,1,1,1,1,1,0,0,0,1,1,1,1,1,1,1],[1,1,1,1,1,1,1,0,0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0,0,0,1,1,1,1,1,1,1,1,1,0,0,1,1,1,1,1,1,1,0,0,0,1,1,1,1,1,1,1],[1,1,1,1,1,1,1,0,0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0,0,0,1,1,1,1,1,1,1,1,1,0,0,1,1,1,1,1,1,1,0,0,0,1,1,1,1,1,1,1],[1,1,1,1,1,1,1,0,0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0,0,0,1,1,1,1,1,1,1,1,1,0,0,1,1,1,1,1,1,1,0,0,0,1,1,1,1,1,1,1],[1,1,1,1,1,1,1,0,0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0,0,0,1,1,1,1,1,1,1,1,1,0,0,1,1,1,1,1,1,1,0,0,0,1,1,1,1,1,1,1],[1,1,1,1,1,1,1,0,0,0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0,0,0,1,1,1,1,1,1,1,1,1,0,0,1,1,1,1,1,1,1,0,0,0,1,1,1,1,1,1,1],[1,1,1,1,1,1,1,1,0,0,0,0,0,0,0,0,0,0,1,1,1,1,1,1,1,1,1,1,1,0,0,0,1,1,1,1,1,1,1,1,1,0,0,0,1,1,1,1,1,1,0,0,0,1,1,1,1,1,1,1],[1,1,1,1,1,1,1,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,1,1,1,1,1,1,0,0,0,0,1,1,1,1,1,1,0,0,0,1,1,1,1,1,1,1],[1,1,1,1,1,1,1,1,1,1,1,1,1,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,1,1,1,1,1,1],[1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,1,1,1,1,1,1,1],[1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1],[1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1],[1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1],[1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1],[1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1]];
const MAZE_COLS = 60;
const MAZE_ROWS = 42;
const CELL_W = 100 / MAZE_COLS;
const CELL_H = 100 / MAZE_ROWS;

// ============================================================
//  CONFIGURACIÓN
// ============================================================
const POS_INICIAL     = { col: 4, row: 7 };
const TIEMPO_TOTAL    = 180;
const PENALIZACION    = 15;
const TOTAL_CORRECTAS = 5;

// Radio de detección de colisión en celdas (tolerancia)
// Si el personaje está a menos de RADIO celdas de distancia, se activa
const RADIO_COLISION = 1.5;

// ============================================================
//  ESTADO
// ============================================================
let playerCol = POS_INICIAL.col;
let playerRow = POS_INICIAL.row;
let correctasRecogidas = 0;
let touchedTiles = new Set();
let segundosRestantes = TIEMPO_TOTAL;
let intervaloTimer = null;
let juegoTerminado = false;

// ============================================================
//  DOM
// ============================================================
const personaje  = document.getElementById('personaje');
const letras     = document.querySelectorAll('.letra');
const meta       = document.querySelector('.meta');
const contadorEl = document.querySelector('.contador');
const timerEl    = document.querySelector('.timer');
const tablero    = document.querySelector('.tablero');
const btnRetry   = document.querySelector('.bottom-panel button:nth-child(1)');
const btnPista   = document.querySelector('.bottom-panel button:nth-child(2)');

// ============================================================
//  HELPERS DE GRILLA
// ============================================================
function esCamino(col, row) {
  if (col < 0 || col >= MAZE_COLS || row < 0 || row >= MAZE_ROWS) return false;
  return MAZE_GRID[row][col] === 0;
}

function celdaAPct(col, row) {
  return {
    x: col * CELL_W + CELL_W / 2,
    y: row * CELL_H + CELL_H / 2
  };
}

// Convierte posición px absoluta a celda de grilla usando el tablero real
function pxACelda(pxX, pxY) {
  const rect = tablero.getBoundingClientRect();
  const xPct = (pxX - rect.left) / rect.width  * 100;
  const yPct = (pxY - rect.top)  / rect.height * 100;
  return {
    col: Math.floor(xPct / CELL_W),
    row: Math.floor(yPct / CELL_H),
    xPct,
    yPct
  };
}

// ============================================================
//  MOVER PERSONAJE
// ============================================================
function mover(dCol, dRow) {
  if (juegoTerminado) return;
  const nc = playerCol + dCol;
  const nr = playerRow + dRow;
  if (!esCamino(nc, nr)) return;

  playerCol = nc;
  playerRow = nr;

  const { x, y } = celdaAPct(playerCol, playerRow);
  personaje.style.left = (x - 3) + '%';
  personaje.style.top  = (y - 5) + '%';

  revisarColisiones();
}

// ============================================================
//  COLISIONES — usa distancia en celdas, no coincidencia exacta
// ============================================================
function revisarColisiones() {
  letras.forEach(letra => {
    const idx = letra.dataset.idx;
    if (touchedTiles.has(idx)) return;

    // Obtener centro de la letra en px
    const r = letra.getBoundingClientRect();
    const cx = r.left + r.width  / 2;
    const cy = r.top  + r.height / 2;
    const lc = pxACelda(cx, cy);

    // Distancia en celdas entre personaje y letra
    const dist = Math.sqrt(
      Math.pow(lc.col - playerCol, 2) +
      Math.pow(lc.row - playerRow, 2)
    );

    if (dist <= RADIO_COLISION) {
      touchedTiles.add(idx);
      if (letra.classList.contains('correcta')) {
        correctasRecogidas++;
        actualizarContador();
        efectoCorrecto(letra);
      } else {
        efectoIncorrecto();
        segundosRestantes = Math.max(0, segundosRestantes - PENALIZACION);
        actualizarTimer();
      }
    }
  });

  // Detectar meta (manzana) — mismo sistema de distancia
  const mr  = meta.getBoundingClientRect();
  const mcx = mr.left + mr.width  / 2;
  const mcy = mr.top  + mr.height / 2;
  const mc  = pxACelda(mcx, mcy);
  const distMeta = Math.sqrt(
    Math.pow(mc.col - playerCol, 2) +
    Math.pow(mc.row - playerRow, 2)
  );

  if (distMeta <= RADIO_COLISION && !juegoTerminado) {
    ganar();
  }
}

// ============================================================
//  EFECTOS VISUALES
// ============================================================
function efectoCorrecto(letra) {
  letra.classList.add('recogida');
}

function efectoIncorrecto() {
  tablero.classList.add('flash-error');
  personaje.classList.add('shake');
  setTimeout(() => {
    tablero.classList.remove('flash-error');
    personaje.classList.remove('shake');
  }, 450);
}

// ============================================================
//  CONTADOR Y TIMER
// ============================================================
function actualizarContador() {
  contadorEl.textContent = `⭐ ${correctasRecogidas}/${TOTAL_CORRECTAS}`;
}

function actualizarTimer() {
  const m = Math.floor(segundosRestantes / 60);
  const s = segundosRestantes % 60;
  timerEl.textContent = `⏰ ${m}:${s.toString().padStart(2, '0')}`;
}

function tickTimer() {
  if (juegoTerminado) return;
  segundosRestantes--;
  if (segundosRestantes <= 0) {
    segundosRestantes = 0;
    actualizarTimer();
    perder();
    return;
  }
  actualizarTimer();
}

// ============================================================
//  GANAR / PERDER  — usan un div overlay, no alert()
// ============================================================
function mostrarOverlay(titulo, sub) {
  // Crear overlay si no existe
  let ov = document.getElementById('game-overlay');
  if (!ov) {
    ov = document.createElement('div');
    ov.id = 'game-overlay';
    ov.style.cssText = `
      position:absolute; inset:0; background:rgba(0,0,0,0.6);
      display:flex; flex-direction:column; align-items:center;
      justify-content:center; gap:12px; z-index:100;
      border-radius: inherit;
    `;
    tablero.style.position = 'relative';
    tablero.appendChild(ov);
  }
  ov.innerHTML = `
    <div style="font-size:2rem;font-weight:700;color:#fff;text-shadow:0 2px 8px rgba(0,0,0,0.5)">${titulo}</div>
    <div style="font-size:1rem;color:rgba(255,255,255,0.85)">${sub}</div>
    <button onclick="reiniciar()" style="
      margin-top:8px;padding:10px 28px;background:#2ecc71;color:#fff;
      border:none;border-radius:24px;font-size:1rem;cursor:pointer;font-family:inherit
    ">Jugar de nuevo</button>
  `;
  ov.style.display = 'flex';
}

function ganar() {
  juegoTerminado = true;
  clearInterval(intervaloTimer);
  mostrarOverlay('¡Felicidades! 🎉', `Ana llegó con ${correctasRecogidas} letras A recogidas`);
}

function perder() {
  juegoTerminado = true;
  clearInterval(intervaloTimer);
  mostrarOverlay('¡Tiempo! ⏰', 'Intenta de nuevo');
}

// ============================================================
//  REINICIAR
// ============================================================
function reiniciar() {
  juegoTerminado    = false;
  correctasRecogidas = 0;
  touchedTiles      = new Set();
  segundosRestantes = TIEMPO_TOTAL;
  playerCol         = POS_INICIAL.col;
  playerRow         = POS_INICIAL.row;

  const { x, y } = celdaAPct(playerCol, playerRow);
  personaje.style.left = (x - 3) + '%';
  personaje.style.top  = (y - 5) + '%';

  actualizarContador();
  actualizarTimer();

  letras.forEach(l => l.classList.remove('recogida'));

  const ov = document.getElementById('game-overlay');
  if (ov) ov.style.display = 'none';

  clearInterval(intervaloTimer);
  intervaloTimer = setInterval(tickTimer, 1000);
}

// ============================================================
//  PISTA — BFS y mueve un paso automático
// ============================================================
function bfs(sc, sr, ec, er) {
  const queue   = [{ c: sc, r: sr, path: [] }];
  const visited = new Set([`${sc},${sr}`]);
  while (queue.length) {
    const { c, r, path } = queue.shift();
    if (c === ec && r === er) return [...path, { c, r }];
    for (const [dc, dr] of [[0,-1],[0,1],[-1,0],[1,0]]) {
      const nc = c+dc, nr = r+dr, k = `${nc},${nr}`;
      if (!visited.has(k) && esCamino(nc, nr)) {
        visited.add(k);
        queue.push({ c: nc, r: nr, path: [...path, { c, r }] });
      }
    }
  }
  return null;
}

function mostrarPista() {
  if (juegoTerminado) return;

  // Encontrar la celda de la meta dinámicamente
  const mr  = meta.getBoundingClientRect();
  const mc  = pxACelda(mr.left + mr.width/2, mr.top + mr.height/2);

  const path = bfs(playerCol, playerRow, mc.col, mc.row);
  if (!path || path.length < 2) return;

  const next = path[1];
  mover(next.c - playerCol, next.r - playerRow);

  personaje.classList.add('pista-highlight');
  setTimeout(() => personaje.classList.remove('pista-highlight'), 600);
}

// ============================================================
//  CONTROLES — teclado
// ============================================================
document.addEventListener('keydown', e => {
  if      (['ArrowUp',   'w','W'].includes(e.key)) { e.preventDefault(); mover( 0,-1); }
  else if (['ArrowDown', 's','S'].includes(e.key)) { e.preventDefault(); mover( 0, 1); }
  else if (['ArrowLeft', 'a','A'].includes(e.key)) { e.preventDefault(); mover(-1, 0); }
  else if (['ArrowRight','d','D'].includes(e.key)) { e.preventDefault(); mover( 1, 0); }
});

// ============================================================
//  CONTROLES — swipe táctil
// ============================================================
let tx = null, ty = null;
document.addEventListener('touchstart', e => {
  tx = e.touches[0].clientX;
  ty = e.touches[0].clientY;
}, { passive: true });
document.addEventListener('touchend', e => {
  if (tx === null) return;
  const dx = e.changedTouches[0].clientX - tx;
  const dy = e.changedTouches[0].clientY - ty;
  if (Math.abs(dx) > Math.abs(dy)) {
    if (dx >  30) mover( 1, 0);
    else if (dx < -30) mover(-1, 0);
  } else {
    if (dy >  30) mover(0,  1);
    else if (dy < -30) mover(0, -1);
  }
  tx = null; ty = null;
}, { passive: true });

// ============================================================
//  BOTONES
// ============================================================
btnRetry.addEventListener('click', reiniciar);
btnPista.addEventListener('click', mostrarPista);

// ============================================================
//  MARCAR LETRAS CORRECTAS/INCORRECTAS + asignar índice único
// ============================================================
letras.forEach((letra, i) => {
  letra.dataset.idx = i;   // ID único para el Set de recogidas
  if (letra.textContent.trim() === 'A') letra.classList.add('correcta');
  else letra.classList.add('incorrecta');
});

// ============================================================
//  INICIO
// ============================================================
reiniciar();

const tiempoTexto = document.getElementById("tiempo");

let tiempo = 180; // 3 minutos en segundos

function actualizarTimer(){

    let minutos = Math.floor(tiempo / 60);
    let segundos = tiempo % 60;

    // FORMATO 3:00
    if(segundos < 10){
        segundos = "0" + segundos;
    }

    tiempoTexto.textContent = minutos + ":" + segundos;

    tiempo--;

    // CUANDO TERMINE
    if(tiempo < 0){

        clearInterval(timer);

        alert("⏰ Tiempo terminado");
    }
}

/* EJECUTAR CADA SEGUNDO */

const timer = setInterval(actualizarTimer, 1000);