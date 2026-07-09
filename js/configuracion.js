/**
 * guardarAjuste()
 * Envía el cambio de un toggle al servidor en segundo plano.
 * @param {string} campo   - nombre del ajuste ('musica', 'efectos', etc.)
 * @param {boolean} valor  - true = activado, false = desactivado
 */
function guardarAjuste(campo, valor) {
  fetch('guardar-ajuste.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ campo, valor })
  })
  .then(r => r.json())
  .then(data => {
    if (!data.ok) console.error('Error al guardar', campo);
  })
  .catch(err => console.error('Error de red:', err));
}


// ══════════════════════════════════════════════════════════
//  FOTOS DE PERFIL
// ══════════════════════════════════════════════════════════

/**
 * previewFoto()
 * Muestra la foto elegida por el usuario al instante,
 * antes de que se guarde en el servidor.
 * @param {HTMLInputElement} input  - el <input type="file">
 * @param {string} idImagen         - id del <img> donde se muestra la preview
 */
function previewFoto(input, idImagen) {
  if (!input.files || !input.files[0]) return;

  // Validar que sea una imagen
  const tipo = input.files[0].type;
  if (!tipo.startsWith('image/')) {
    alert('Por favor selecciona un archivo de imagen (JPG, PNG, etc.)');
    input.value = '';
    return;
  }

  // Validar tamaño máximo: 2MB
  const maxMB = 2;
  if (input.files[0].size > maxMB * 1024 * 1024) {
    alert(`La imagen no puede pesar más de ${maxMB}MB. Por favor elige una más pequeña.`);
    input.value = '';
    return;
  }

  // Mostrar preview instantánea
  const reader = new FileReader();
  reader.onload = function(e) {
    document.getElementById(idImagen).src = e.target.result;
  };
  reader.readAsDataURL(input.files[0]);
}


// ══════════════════════════════════════════════════════════
//  GUARDAR FORMULARIO DE CONFIGURACIÓN (fetch + FormData)
// ══════════════════════════════════════════════════════════

/**
 * guardarPerfil()
 * Envía el formulario de Mi Cuenta al servidor sin recargar la página.
 * Muestra un mensaje de éxito/error inline.
 * @param {Event} e - el evento submit del formulario
 */
function guardarPerfil(e) {
  e.preventDefault(); // Evita que el navegador recargue la página

  const form    = document.getElementById('formPerfil');
  const btnGuardar = document.getElementById('btnGuardar');
  const msgArea = document.getElementById('msgPerfil');

  // Indicador visual de carga
  btnGuardar.disabled = true;
  btnGuardar.innerHTML = '⏳ Guardando...';
  msgArea.innerHTML = '';

  // FormData captura automáticamente textos Y archivos del formulario
  const datos = new FormData(form);

  fetch('php/actualizar-perfil.php', {
    method: 'POST',
    body: datos   // sin Content-Type header: el navegador lo pone solo con boundary
  })
  .then(r => r.json())
  .then(data => {
    if (data.ok) {
      msgArea.innerHTML = `
        <div style="background:#e8f5e9;color:#2e7d32;padding:10px 16px;
                    border-radius:8px;font-weight:700;font-size:.88rem;">
          ✅ ¡Perfil actualizado con éxito!
        </div>`;

      // Si el servidor devuelve las nuevas fotos, actualizamos la sesión visual
      // (el navbar y otros elementos que usen el nombre se actualizan al recargar)
      if (data.foto_nino) {
        document.querySelectorAll('.avatar-nino-live').forEach(img => {
          img.src = 'images/perfiles/' + data.foto_nino;
        });
      }
      if (data.foto_padre) {
        document.querySelectorAll('.avatar-padre-live').forEach(img => {
          img.src = 'images/perfiles/' + data.foto_padre;
        });
      }

    } else {
      msgArea.innerHTML = `
        <div style="background:#fdecea;color:#c62828;padding:10px 16px;
                    border-radius:8px;font-weight:700;font-size:.88rem;">
          ❌ ${data.msg || 'Error al guardar. Intenta de nuevo.'}
        </div>`;
    }
  })
  .catch(err => {
    msgArea.innerHTML = `
      <div style="background:#fdecea;color:#c62828;padding:10px 16px;
                  border-radius:8px;font-weight:700;font-size:.88rem;">
        ❌ Error de conexión. Revisa tu internet.
      </div>`;
    console.error(err);
  })
  .finally(() => {
    btnGuardar.disabled = false;
    btnGuardar.innerHTML = '💾 Guardar cambios';
  });
}

// Conectar el formulario cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('formPerfil');
  if (form) {
    form.addEventListener('submit', guardarPerfil);
  }
});