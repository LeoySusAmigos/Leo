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