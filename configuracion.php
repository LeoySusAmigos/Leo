<?php
session_start();

// ── 1. PROTECCIÓN ───────────────────────────────────────
if (!isset($_SESSION['userID'])) {
    header('Location: login.php');
    exit;
}

// ── 2. CONEXIÓN A LA BASE DE DATOS ─────────────────────
$host    = 'localhost';
$db      = 'leo_and_friends';
$dbUser  = 'root';
$dbPass  = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    die('<p style="font-family:sans-serif;color:red;padding:20px;">
         Error de conexión a la base de datos. Intenta más tarde.</p>');
}

// ── 3. OBTENER DATOS DEL USUARIO ───────────────────────
// Agregamos edad_nino a la consulta
$stmt = $pdo->prepare('SELECT userID, nombre_nino, edad_nino, nombre_papa, correo, rol, fecha_registro, foto_nino, foto_padre
                        FROM usuarios
                        WHERE userID = ?
                        LIMIT 1');
$stmt->execute([$_SESSION['userID']]);
$fila = $stmt->fetch();

if (!$fila) {
    session_destroy();
    header('Location: login.php');
    exit;
}

// ── 4. PROGRESO (puntos reales de la BD) ────────────────
$stmtProg = $pdo->prepare('SELECT puntos FROM progreso WHERE userID = ? LIMIT 1');
$stmtProg->execute([$fila['userID']]);
$progreso = $stmtProg->fetch();
$puntos   = $progreso['puntos'] ?? 0;

// ── 5. PAQUETE ACTIVO DEL USUARIO ──────────────────────
$stmtPaq = $pdo->prepare('SELECT p.nombre, p.precio
                           FROM suscripciones s
                           JOIN paquetes p ON s.paqueteID = p.paqueteID
                           WHERE s.userID = ? AND s.estado = "activa"
                           LIMIT 1');
$stmtPaq->execute([$fila['userID']]);
$paqueteActivo = $stmtPaq->fetch();

$planTexto = $paqueteActivo
    ? $paqueteActivo['nombre'] . ' — $' . number_format($paqueteActivo['precio'], 2) . '/mes'
    : 'Sin plan activo';

// ── 6. VARIABLES PARA LA VISTA ──────────────────────────
$usuario = [
    'id'     => $fila['userID'],
    'nombre' => $fila['nombre_papa'],
    'correo' => $fila['correo'],
    'rol'    => $fila['rol'],
    'foto'   => !empty($fila['foto_padre'])
                ? 'images/perfiles/' . $fila['foto_padre']
                : 'images/default-padre.png',
    'plan'   => $planTexto,
];

$nino = [
    'nombre'    => $fila['nombre_nino'],
    'edad'      => $fila['edad_nino'] ?? 0,
    'foto'      => !empty($fila['foto_nino'])
                   ? 'images/perfiles/' . $fila['foto_nino']
                   : 'images/default-nino.png',
    'puntos'    => $puntos,
    'musica'    => true,
    'efectos'   => true,
    'narracion' => true,
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Configuración</title>
  <link rel="shortcut icon" href="images/favicon/favicon-32x32.png" type="image/x-icon">
  <link href="https://fonts.googleapis.com/css2?family=Balsamiq+Sans:wght@700&family=Fredoka:wght@600;900&family=Nunito:wght@700;900&family=Quicksand:wght@500;700;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="stylesheet" href="styles/navbar.css" />
  <link rel="stylesheet" href="styles/configuracion.css" />
</head>
<body>

<?php include 'components/navbar.php'; ?>

<main class="main">

  <h1 class="page-title">Configuración</h1>

  <!-- ══════════════════════════════
       SECCIÓN 1: MI CUENTA
  ══════════════════════════════ -->
  <div class="seccion">
    <p class="seccion__titulo">1. Mi cuenta</p>

    <details class="accordion" open>
      <summary>
        Mi cuenta
        <span class="flecha">▼</span>
      </summary>

      <div class="accordion__body">

        <?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
          <div style="background:#e8f5e9;color:#2e7d32;padding:10px 16px;border-radius:8px;margin:12px 16px;font-weight:700;font-size:.88rem;">
            <i class="fa-regular fa-circle-check" style="color: rgb(33, 168, 30);"></i> ¡Perfil actualizado con éxito!
          </div>
        <?php endif; ?>

        <form id="formPerfil" method="POST" action="php/actualizar-perfil.php" enctype="multipart/form-data">

          <div id="msgPerfil" style="padding:0 16px;"></div>

          <!-- Solo avatar del adulto en Mi Cuenta -->
          <div class="fila" style="gap:20px;flex-wrap:wrap;">
            <div style="display:flex;flex-direction:column;align-items:center;gap:8px;">
              <span style="font-size:.75rem;font-weight:700;color:#aaa;">
                <i class="fa-solid fa-user-tie"></i> Avatar del Adulto
              </span>
              <div style="position:relative;width:72px;height:72px;">
                <?php if (!empty($fila['foto_padre'])): ?>
                  <img id="prevPadre"
                       src="<?= htmlspecialchars($usuario['foto']) ?>"
                       style="width:72px;height:72px;border-radius:50%;object-fit:cover;border:3px solid #42a5f5;" />
                <?php else: ?>
                  <div id="prevPadre" style="
                      width:72px;height:72px;border-radius:50%;
                      border:3px solid #42a5f5;background:#e3f2fd;
                      display:flex;align-items:center;justify-content:center;">
                    <i class="fa-solid fa-user-tie" style="font-size:1.8rem;color:#42a5f5;"></i>
                  </div>
                <?php endif; ?>
                <label for="foto_padre" style="
                  position:absolute;bottom:0;right:0;
                  width:24px;height:24px;border-radius:50%;
                  background:#42a5f5;color:#fff;
                  display:flex;align-items:center;justify-content:center;
                  cursor:pointer;font-size:.7rem;
                  box-shadow:0 2px 6px rgba(0,0,0,.25);border:2px solid #fff;">
                  <i class="fa-solid fa-camera"></i>
                </label>
                <input type="file" id="foto_padre" name="foto_padre" accept="image/*"
                       style="display:none;" onchange="previewFoto(this,'prevPadre')">
              </div>
            </div>
          </div>

          <!-- Nombre del papá -->
          <div class="fila">
            <div class="fila__icono"><i class="fa-solid fa-user-shield"></i></div>
            <div class="fila__info">
              <label class="fila__label" for="nombre_papa">Nombre del papá/mamá</label>
            </div>
            <div class="fila__derecha">
              <input type="text" id="nombre_papa" name="nombre_papa"
                     value="<?= htmlspecialchars($usuario['nombre']) ?>"
                     required
                     style="border:1px solid #ddd;border-radius:8px;padding:6px 12px;font-family:inherit;font-size:.9rem;width:180px;">
            </div>
          </div>

          <!-- Correo -->
          <div class="fila">
            <div class="fila__icono"><i class="fa-regular fa-envelope"></i></div>
            <div class="fila__info">
              <label class="fila__label" for="correo">Correo electrónico</label>
            </div>
            <div class="fila__derecha">
              <input type="email" id="correo" name="correo"
                     value="<?= htmlspecialchars($usuario['correo']) ?>"
                     required
                     style="border:1px solid #ddd;border-radius:8px;padding:6px 12px;font-family:inherit;font-size:.9rem;width:200px;">
            </div>
          </div>

          <!-- Botón guardar -->
          <div style="padding:14px 20px;text-align:right;border-top:1px solid #f0f0f0;">
            <button type="submit" id="btnGuardar" class="btn btn--outline-verde"
                    style="background:#2d9e4e;color:#fff;border-color:#2d9e4e;">
              <i class="fa-solid fa-floppy-disk"></i> Guardar cambios
            </button>
          </div>

        </form>

        <!-- Cambiar contraseña -->
        <div class="fila">
          <div class="fila__icono"><i class="fa-solid fa-lock"></i></div>
          <div class="fila__info">
            <div class="fila__label">Cambiar contraseña</div>
          </div>
          <div class="fila__derecha">
            <a href="recuperar-password.html" class="arrow-link">
              <i class="fa-solid fa-chevron-right"></i>
            </a>
          </div>
        </div>

        <!-- Plan actual -->
        <div class="fila">
          <div class="fila__icono"><i class="fa-solid fa-crown"></i></div>
          <div class="fila__info">
            <div class="fila__label">Plan actual</div>
            <div class="fila__sub"><?= htmlspecialchars($usuario['plan']) ?></div>
          </div>
          <div class="fila__derecha">
            <a href="paquetes-salvajes.php" class="btn btn--outline-verde">Gestionar plan</a>
          </div>
        </div>

      </div><!-- /.accordion__body -->
    </details>
  </div>


  <!-- ══════════════════════════════
       SECCIÓN 2: CONFIGURACIÓN DEL NIÑO
  ══════════════════════════════ -->
  <div class="seccion">
    <p class="seccion__titulo">2. Configuración del niño</p>

    <details class="accordion" open>
      <summary>
        Configuración del niño
        <span class="flecha">▼</span>
      </summary>

      <div class="accordion__body">

        <!-- Avatar del niño + info -->
        <div class="perfil-fila">

          <div style="position:relative;width:54px;height:54px;flex-shrink:0;">
            <?php if (!empty($fila['foto_nino'])): ?>
              <img id="prevNino"
                   src="<?= htmlspecialchars($nino['foto']) ?>"
                   style="width:54px;height:54px;border-radius:50%;object-fit:cover;border:3px solid #2d9e4e;" />
            <?php else: ?>
              <div id="prevNino" style="
                  width:54px;height:54px;border-radius:50%;
                  border:3px solid #ffca28;background:#fff9e6;
                  display:flex;align-items:center;justify-content:center;">
                <i class="fa-solid fa-child" style="font-size:1.4rem;color:#ffca28;"></i>
              </div>
            <?php endif; ?>
            <label for="foto_nino" style="
              position:absolute;bottom:0;right:0;
              width:20px;height:20px;border-radius:50%;
              background:#ffca28;color:#fff;
              display:flex;align-items:center;justify-content:center;
              cursor:pointer;font-size:.6rem;
              box-shadow:0 2px 6px rgba(0,0,0,.25);border:2px solid #fff;">
              <i class="fa-solid fa-camera"></i>
            </label>
            <input type="file" id="foto_nino" name="foto_nino" accept="image/*"
                   form="formPerfil"
                   style="display:none;" onchange="previewFoto(this,'prevNino')">
          </div>

          <div class="perfil-fila__datos">
            <div class="perfil-fila__nombre"><?= htmlspecialchars($nino['nombre']) ?></div>
            <div class="perfil-fila__correo">
              ⭐ <?= (int)$nino['puntos'] ?> puntos
              &nbsp;·&nbsp;
              <?= (int)$nino['edad'] ?> años
            </div>
          </div>

        </div>

        <!-- Nombre del niño (editable, ligado al formPerfil con form=) -->
        <div class="fila">
          <div class="fila__icono"><i class="fa-solid fa-child"></i></div>
          <div class="fila__info">
            <label class="fila__label" for="nombre_nino">Nombre del niño/a</label>
          </div>
          <div class="fila__derecha">
            <input type="text" id="nombre_nino" name="nombre_nino"
                   value="<?= htmlspecialchars($nino['nombre']) ?>"
                   form="formPerfil"
                   required
                   style="border:1px solid #ddd;border-radius:8px;padding:6px 12px;font-family:inherit;font-size:.9rem;width:180px;">
          </div>
        </div>

        <!-- Edad del niño (editable) -->
        <div class="fila">
          <div class="fila__icono"><i class="fa-solid fa-cake-candles"></i></div>
          <div class="fila__info">
            <label class="fila__label" for="edad_nino">Edad del niño/a</label>
          </div>
          <div class="fila__derecha">
            <input type="number" id="edad_nino" name="edad_nino"
                   value="<?= (int)$nino['edad'] ?>"
                   min="6" max="12"
                   form="formPerfil"
                   style="border:1px solid #ddd;border-radius:8px;padding:6px 12px;font-family:inherit;font-size:.9rem;width:80px;">
          </div>
        </div>

        <!-- Sonido -->
        <div class="fila">
          <div class="fila__icono"><i class="fa-solid fa-volume-high"></i></div>
          <div class="fila__info">
            <div class="fila__label">Sonido</div>
          </div>
          <div class="fila__derecha">
            <div class="toggles-grupo">

              <div class="toggle-wrap">
                <span class="toggle-label">Música</span>
                <label class="toggle">
                  <input type="checkbox" name="musica" value="1"
                         <?= $nino['musica'] ? 'checked' : '' ?>
                         onchange="guardarAjuste('musica', this.checked)" />
                  <span class="toggle-slider"></span>
                </label>
              </div>

              <div class="toggle-wrap">
                <span class="toggle-label">Efectos</span>
                <label class="toggle">
                  <input type="checkbox" name="efectos" value="1"
                         <?= $nino['efectos'] ? 'checked' : '' ?>
                         onchange="guardarAjuste('efectos', this.checked)" />
                  <span class="toggle-slider"></span>
                </label>
              </div>

              <div class="toggle-wrap">
                <span class="toggle-label">Narración</span>
                <label class="toggle">
                  <input type="checkbox" name="narracion" value="1"
                         <?= $nino['narracion'] ? 'checked' : '' ?>
                         onchange="guardarAjuste('narracion', this.checked)" />
                  <span class="toggle-slider"></span>
                </label>
              </div>

            </div>
          </div>
        </div>

      </div>
    </details>
  </div>


  <!-- ══════════════════════════════
       SECCIÓN 3: CUENTA
  ══════════════════════════════ -->
  <div class="seccion">
    <p class="seccion__titulo">3. Cuenta</p>

    <details class="accordion" open>
      <summary>
        Cuenta
        <span class="flecha">▼</span>
      </summary>

      <div class="accordion__body">
        <button type="submit" class="btn--verde" onclick="window.location.href='index.php'">
            <i class="fa-solid fa-house" style="color: #1a6e2e;"></i>
            Volver a Página Principal
          </button>
        <form method="POST" action="php/logout.php">
          <button type="submit" class="btn--rojo">
            <i class="fa-solid fa-right-from-bracket"></i>
            Cerrar sesión
          </button>
        </form>
      </div>
    </details>
  </div>

  <p class="footer-version">Leo &amp; Friends v2.1.0</p>

</main>

<!-- Botón flotante volver a inicio -->
<a onclick="window.history.back()" id="btnVolver" title="Volver al inicio"
   style="
    position:fixed;bottom:28px;left:28px;
    width:52px;height:52px;border-radius:50%;
    background:linear-gradient(135deg,#1a6e2e,#2d9e4e);
    color:#fff;display:flex;align-items:center;justify-content:center;
    font-size:1.3rem;text-decoration:none;
    box-shadow:0 4px 16px rgba(0,0,0,.25);
    transition:transform .25s ease,box-shadow .25s ease;
    z-index:999;">
  <i class="fa-solid fa-house"></i>
</a>

<style>
#btnVolver:hover {
    transform: scale(1.12) translateY(-3px);
    box-shadow: 0 8px 24px rgba(0,0,0,.3);
}
@media (max-width: 425px) {
    #btnVolver { width:44px;height:44px;font-size:1.1rem;bottom:16px;left:16px; }
}
</style>

<script src="js/configuracion.js"></script>
<script src="js/navbar.js"></script>

<script>
function previewFoto(input, idImagen) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var elemento = document.getElementById(idImagen);
            if (elemento.tagName === 'DIV') {
                var img = document.createElement('img');
                img.id = idImagen;
                img.style.cssText = 'width:' + elemento.style.width
                    + ';height:' + elemento.style.height
                    + ';border-radius:50%;object-fit:cover;'
                    + 'border:' + elemento.style.border + ';';
                elemento.parentNode.replaceChild(img, elemento);
                elemento = img;
            }
            elemento.src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

</body>
</html>