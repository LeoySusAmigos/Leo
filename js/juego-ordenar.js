
const NUM_FRANJAS = 10;
const API_URL = 'php/ordenar-puzzle.php';


let slices = [], order = [], solving = false, dragSrc = null, touchSrc = null;

async function cargarLibros() {
  try {
    const res = await fetch(API_URL);
    const libros = await res.json();
    const grid = document.getElementById('gridLibros');
    grid.innerHTML = '';

    if (libros.error) {
      grid.innerHTML = `<div class="loading">Error: ${libros.error}</div>`;
      return;
    }
    if (!libros.length) {
      grid.innerHTML = '<div class="loading">No hay libros disponibles.</div>';
      return;
    }

    libros.forEach(libro => {
      const card = document.createElement('div');
      card.className = 'libro-card';
      card.innerHTML = `
        <img src="${libro.portada_url}" alt="${libro.titulo}"
             onerror="this.src='https://placehold.co/160x240?text=Sin+imagen'" />
        <div class="titulo">${libro.titulo}</div>
      `;
      card.addEventListener('click', () => iniciarJuego(libro.portada_url, libro.titulo));
      grid.appendChild(card);
    });

  } catch (err) {
    document.getElementById('gridLibros').innerHTML =
      '<div class="loading">⚠️ Error al cargar libros. ¿Está corriendo XAMPP?</div>';
  }
}

function iniciarJuego(urlPortada, titulo) {
  const img = new Image();
  img.crossOrigin = 'anonymous';
  img.onload = () => {
    slices = cortarImagen(img);
    order  = Array.from({length: NUM_FRANJAS}, (_, i) => i);
    mezclar();
    document.getElementById('tituloLibro').textContent = '📖 ' + titulo;
    document.getElementById('pantallaLibros').style.display = 'none';
    document.getElementById('pantallaJuego').style.display  = 'block';
    document.getElementById('winMsg').style.display = 'none';
    document.getElementById('info').textContent = 'Arrastrá las franjas para ordenarlas.';
  };
  img.onerror = () => alert('No se pudo cargar la imagen: ' + urlPortada);
  img.src = urlPortada;
}

function cortarImagen(img) {
  const W  = 400;
  const H  = Math.round(W * img.height / img.width);
  const fh = img.height / NUM_FRANJAS;
  return Array.from({length: NUM_FRANJAS}, (_, i) => {
    const c = document.createElement('canvas');
    c.width  = W;
    c.height = Math.round(H / NUM_FRANJAS);
    c.getContext('2d').drawImage(img, 0, i * fh, img.width, fh, 0, 0, W, c.height);
    return c;
  });
}

function mezclar() {
  do {
    for (let i = order.length - 1; i > 0; i--) {
      const j = Math.floor(Math.random() * (i + 1));
      [order[i], order[j]] = [order[j], order[i]];
    }
  } while (checkWin());
  renderStrips();
  actualizarProgreso();
  document.getElementById('winMsg').style.display = 'none';
  document.getElementById('info').textContent = 'Arrastrá las franjas para ordenarlas.';
}

function renderStrips() {
  const wrap = document.getElementById('stripsWrap');
  wrap.innerHTML = '';

  order.forEach((src, pos) => {
    const div = document.createElement('div');
    div.className   = 'strip';
    div.draggable   = true;
    div.dataset.pos = pos;

    // Canvas de la franja
    const c = document.createElement('canvas');
    c.width  = slices[src].width;
    c.height = slices[src].height;
    c.getContext('2d').drawImage(slices[src], 0, 0);
    div.appendChild(c);

    // Número de posición
    const lbl = document.createElement('span');
    lbl.className   = 'lbl';
    lbl.textContent = pos + 1;
    div.appendChild(lbl);

    // Ícono de arrastre
    const grip = document.createElement('span');
    grip.className   = 'grip';
    grip.textContent = '⠿';
    div.appendChild(grip);

    // ── Drag & Drop (escritorio) ──
    div.addEventListener('dragstart', e => {
      dragSrc = pos;
      setTimeout(() => e.target.classList.add('dragging'), 0);
      e.dataTransfer.effectAllowed = 'move';
    });
    div.addEventListener('dragend', () =>
      document.querySelectorAll('.strip').forEach(s => s.classList.remove('dragging', 'over'))
    );
    div.addEventListener('dragover', e => {
      e.preventDefault();
      document.querySelectorAll('.strip').forEach(s => s.classList.remove('over'));
      e.currentTarget.classList.add('over');
    });
    div.addEventListener('dragleave', e => e.currentTarget.classList.remove('over'));
    div.addEventListener('drop', e => {
      e.preventDefault();
      moverFranja(dragSrc, parseInt(e.currentTarget.dataset.pos));
    });

    // ── Touch (tablet / celular) ──
    div.addEventListener('touchstart', () => { touchSrc = pos; }, { passive: true });
    div.addEventListener('touchend', e => {
      const t  = e.changedTouches[0];
      const el = document.elementFromPoint(t.clientX, t.clientY);
      const sd = el && el.closest('.strip');
      if (sd && touchSrc !== null) moverFranja(touchSrc, parseInt(sd.dataset.pos));
      touchSrc = null;
    });

    wrap.appendChild(div);
  });
}

function moverFranja(desde, hasta) {
  if (desde === null || desde === hasta) return;
  const moved = order.splice(desde, 1)[0];
  order.splice(hasta, 0, moved);
  renderStrips();
  actualizarProgreso();
  if (checkWin()) {
    document.getElementById('winMsg').style.display = 'block';
    document.getElementById('info').textContent = '';
  } else {
    document.getElementById('info').textContent = '¡Bien! Seguí intentando 💪';
  }
}

function checkWin() {
  return order.every((v, i) => v === i);
}

function actualizarProgreso() {
  const correct = order.filter((v, i) => v === i).length;
  const pct = Math.round(correct / NUM_FRANJAS * 100);
  document.getElementById('progFill').style.width = pct + '%';
  document.getElementById('progLbl').textContent  = pct + '%';
}

document.getElementById('ayudaBtn').addEventListener('click', async () => {
  if (solving) return;
  solving = true;
  ['ayudaBtn', 'mezclarBtn', 'volverBtn'].forEach(id => document.getElementById(id).disabled = true);
  document.getElementById('info').textContent = 'Ordenando poquito a poco... 🤖';

  for (let i = 0; i < order.length - 1; i++) {
    for (let j = 0; j < order.length - i - 1; j++) {
      if (order[j] > order[j + 1]) {
        [order[j], order[j + 1]] = [order[j + 1], order[j]];
        renderStrips();
        actualizarProgreso();
        await new Promise(r => setTimeout(r, 180));
      }
    }
  }

  solving = false;
  ['ayudaBtn', 'mezclarBtn', 'volverBtn'].forEach(id => document.getElementById(id).disabled = false);
  document.getElementById('winMsg').style.display = 'block';
  document.getElementById('info').textContent = '';
  actualizarProgreso();
});

document.getElementById('mezclarBtn').addEventListener('click', () => {
  if (!solving) mezclar();
});

document.getElementById('volverBtn').addEventListener('click', () => {
  if (solving) return;
  document.getElementById('pantallaJuego').style.display  = 'none';
  document.getElementById('pantallaLibros').style.display = 'block';
});

cargarLibros();