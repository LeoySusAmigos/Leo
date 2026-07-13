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
$stmt = $pdo->prepare('SELECT userID, nombre_nino, nombre_papa, correo, rol,
                               fecha_registro, foto_nino, foto_padre
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
 
// ── 4. PROGRESO (estrellas) ─────────────────────────────
$stmtProg = $pdo->prepare('SELECT COUNT(*) AS estrellas FROM progreso WHERE userID = ?');
$stmtProg->execute([$fila['userID']]);
$progreso  = $stmtProg->fetch();
$estrellas = $progreso['estrellas'] ?? 0;
 
// ── 5. PAQUETE ACTIVO DEL USUARIO ──────────────────────
$stmtPaq = $pdo->prepare('SELECT p.nombre, p.precio
                           FROM suscripciones s
                           JOIN paquetes p ON s.paqueteID = p.paqueteID
                           WHERE s.userID = ? AND s.estado = "activa"
                           LIMIT 1');
$stmtPaq->execute([$fila['userID']]);
$paqueteActivo = $stmtPaq->fetch();
 
// Si tiene paquete activo lo mostramos, si no tiene ninguno mostramos "Sin plan activo"
if ($paqueteActivo) {
    $planTexto = $paqueteActivo['nombre'] . ' — $' . number_format($paqueteActivo['precio'], 2) . '/mes';
} else {
    $planTexto = 'Sin plan activo';
}
 
// ── 5. VARIABLES PARA LA VISTA ──────────────────────────
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
    'foto'      => !empty($fila['foto_nino'])
                   ? 'images/perfiles/' . $fila['foto_nino']
                   : 'images/default-nino.png',
    'estrellas' => $estrellas,
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
            ✅ ¡Perfil actualizado con éxito!
          </div>
        <?php endif; ?>

        <!-- IMPORTANTE: los inputs de abajo NO se guardan automáticamente.
             Solo se envían a la BD cuando el usuario hace clic en "Guardar cambios".
             Esto ya es el comportamiento normal de un <form>: escribir en un input
             NUNCA dispara un guardado por sí solo, solo el evento "submit" del form. -->
        <form id="formPerfil" method="POST" action="php/actualizar-perfil.php" enctype="multipart/form-data">

          <div id="msgPerfil" style="padding:0 16px;"></div>

          <!-- Avatares -->
          <div class="fila" style="gap:20px;flex-wrap:wrap;">

            <div style="display:flex;flex-direction:column;align-items:center;gap:8px;">
              <span style="font-size:.75rem;font-weight:700;color:#aaa;">
                <i class="fa-solid fa-child"></i> Avatar del Pequeño
              </span>
              <div style="position:relative;width:72px;height:72px;">
                <img id="prevNino"
                    src="<?= htmlspecialchars($nino['foto']) ?>"
                    style="width:72px;height:72px;border-radius:50%;object-fit:cover;border:3px solid #ffca28;" />
                <!-- Botón cámara encima de la foto -->
                <label for="foto_nino" style="
                  position:absolute;bottom:0;right:0;
                  width:24px;height:24px;border-radius:50%;
                  background:#ffca28;color:#fff;
                  display:flex;align-items:center;justify-content:center;
                  cursor:pointer;font-size:.7rem;
                  box-shadow:0 2px 6px rgba(0,0,0,.25);
                  border:2px solid #fff;">
                  <i class="fa-solid fa-camera"></i>
                </label>
                <input type="file" id="foto_nino" name="foto_nino" accept="image/*"
                      style="display:none;"
                      onchange="previewFoto(this,'prevNino')">
              </div>
            </div>

            <div style="display:flex;flex-direction:column;align-items:center;gap:8px;">
              <span style="font-size:.75rem;font-weight:700;color:#aaa;">
                <i class="fa-solid fa-user-tie"></i> Avatar del Adulto
              </span>
              <div style="position:relative;width:72px;height:72px;">
                <img id="prevPadre"
                    src="<?= htmlspecialchars($usuario['foto']) ?>"
                    style="width:72px;height:72px;border-radius:50%;object-fit:cover;border:3px solid #42a5f5;" />
                <!-- Botón cámara encima de la foto -->
                <label for="foto_padre" style="
                  position:absolute;bottom:0;right:0;
                  width:24px;height:24px;border-radius:50%;
                  background:#42a5f5;color:#fff;
                  display:flex;align-items:center;justify-content:center;
                  cursor:pointer;font-size:.7rem;
                  box-shadow:0 2px 6px rgba(0,0,0,.25);
                  border:2px solid #fff;">
                  <i class="fa-solid fa-camera"></i>
                </label>
                <input type="file" id="foto_padre" name="foto_padre" accept="image/*"
                      style="display:none;"
                      onchange="previewFoto(this,'prevPadre')">
              </div>
            </div>

          </div>

          <!-- Nombre del niño -->
          <div class="fila">
            <div class="fila__icono"><i class="fa-solid fa-child"></i></div>
            <div class="fila__info">
              <label class="fila__label" for="nombre_nino">Nombre del niño/a</label>
            </div>
            <div class="fila__derecha">
              <input type="text" id="nombre_nino" name="nombre_nino"
                     value="<?= htmlspecialchars($nino['nombre']) ?>"
                     required
                     style="border:1px solid #ddd;border-radius:8px;padding:6px 12px;font-family:inherit;font-size:.9rem;width:180px;">
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

          <!-- Botón guardar: ÚNICO disparador de guardado -->
          <div style="padding:14px 20px;text-align:right;border-top:1px solid #f0f0f0;">
            <button type="submit" id="btnGuardar" class="btn btn--outline-verde"
                    style="background:#2d9e4e;color:#fff;border-color:#2d9e4e;">
              <i class="fa-solid fa-floppy-disk"></i> Guardar cambios
            </button>
          </div>

        </form>

        <!-- Cambiar contraseña → lleva a tu página de recuperación -->
        <div class="fila">
          <div class="fila__icono"><i class="fa-solid fa-lock"></i></div>
          <div class="fila__info">
            <div class="fila__label">Cambiar contraseña</div>
          </div>
          <div class="fila__derecha">
            <a href="recuperar-contrasena.php" class="arrow-link">
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

        <div class="perfil-fila">
          <img class="avatar-lg"
               src="<?= htmlspecialchars($nino['foto']) ?>"
               alt="Avatar de <?= htmlspecialchars($nino['nombre']) ?>" />
          <div class="perfil-fila__datos">
            <div class="perfil-fila__nombre"><?= htmlspecialchars($nino['nombre']) ?></div>
            <div class="perfil-fila__correo">⭐ <?= (int)$nino['estrellas'] ?> estrellas</div>
          </div>
          <a href="editar-nino.php" class="btn btn--outline-verde">Editar</a>
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

    <details class="accordion">
      <summary>
        Cuenta
        <span class="flecha">▼</span>
      </summary>

      <div class="accordion__body">

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

  <!-- Botón flotante de regreso a inicio -->
  <a href="inicio-nino.php" id="btnVolver" title="Volver al inicio"
    style="
      position:fixed;
      bottom:28px;
      left:28px;
      width:52px;height:52px;
      border-radius:50%;
      background:linear-gradient(135deg, #1a6e2e, #2d9e4e);
      color:#fff;
      display:flex;align-items:center;justify-content:center;
      font-size:1.3rem;
      text-decoration:none;
      box-shadow:0 4px 16px rgba(0,0,0,.25);
      transition:transform .25s ease, box-shadow .25s ease;
      z-index:999;">
    <i class="fa-solid fa-house"></i>
  </a>

  <style>
    #btnVolver:hover {
        transform: scale(1.12) translateY(-3px);
        box-shadow: 0 8px 24px rgba(0,0,0,.3);
    }

    @media (max-width: 425px) {
        #btnVolver {
            width: 44px;
            height: 44px;
            font-size: 1.1rem;
            bottom: 16px;
            left: 16px;
        }
    }
  </style>

</main>

<script src="js/configuracion.js"></script>
<script src="js/navbar.js"></script>

<script>
function previewFoto(input, idImagen) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById(idImagen).src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

</body>
</html>