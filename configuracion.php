<?php
// ════════════════════════════════════════════════════════
//  configuracion.php — Leo & Friends
//  Archivo principal: solo HTML + llamadas a config.php / style.css / script.js
// ════════════════════════════════════════════════════════
require_once 'configuracion.php';   // ← trae $usuario, $nino y la conexión $pdo · PHP
// ════════════════════════════════════════════════════════
//  config.php — Lógica y conexión a base de datos
//  Leo & Friends  |  Base de datos: leo_and_friends  |  Tabla: usuarios
// ════════════════════════════════════════════════════════
session_start();
 
// ── 1. PROTECCIÓN: si no hay sesión activa, redirige al login ───────────────
if (!isset($_SESSION['userID'])) {
    header('Location: login.php');
    exit;
}
 
// ── 2. CONEXIÓN A LA BASE DE DATOS ─────────────────────────────────────────
//    Ajusta host/usuario/contraseña si tu servidor es diferente
$host    = 'localhost';
$db      = 'leo_and_friends';
$dbUser  = 'root';          // ← cambia si tienes otro usuario MySQL
$dbPass  = '';               // ← cambia si tienes contraseña
$charset = 'utf8mb4';
 
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
 
try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    // En producción nunca muestres el error real al usuario
    die('<p style="font-family:sans-serif;color:red;padding:20px;">
         Error de conexión a la base de datos. Intenta más tarde.</p>');
}
 
// ── 3. OBTENER DATOS DEL USUARIO EN SESIÓN ──────────────────────────────────
//    Columnas reales: userID | nombre_nino | correo | password | rol | fecha_registro
$stmt = $pdo->prepare('SELECT userID, nombre_nino, correo, rol, fecha_registro
                        FROM usuarios
                        WHERE userID = ?
                        LIMIT 1');
$stmt->execute([$_SESSION['userID']]);
$fila = $stmt->fetch();
 
// Si el userID de sesión no existe en BD, cierra sesión por seguridad
if (!$fila) {
    session_destroy();
    header('Location: login.php');
    exit;
}
 
// ── 4. OBTENER PROGRESO DEL NIÑO (tabla: progreso) ──────────────────────────
//    Usamos COUNT de registros como "estrellas" acumuladas.
//    Ajusta la consulta si tu tabla progreso tiene otra estructura.
$stmtProg = $pdo->prepare('SELECT COUNT(*) AS estrellas FROM progreso WHERE userID = ?');
$stmtProg->execute([$fila['userID']]);
$progreso = $stmtProg->fetch();
$estrellas = $progreso['estrellas'] ?? 0;
 
// ── 5. PREPARAR VARIABLES PARA LA VISTA ────────────────────────────────────
$usuario = [
    'id'      => $fila['userID'],
    'nombre'  => $fila['nombre_nino'],           // nombre_nino = nombre del niño registrado
    'correo'  => $fila['correo'],
    'rol'     => $fila['rol'],                   // 'usuario' según tus datos
    'desde'   => date('d/m/Y', strtotime($fila['fecha_registro'])),
    'foto'    => 'img/avatar-nino.png',          // ← imagen del usuario
    'idioma'  => 'Español',                      // puedes añadir col. "idioma" a la BD si quieres
    'plan'    => 'Plan Safari — $12.99/mes',     // puedes añadir col. "plan" a la BD si quieres
];
 
// Nota: en tu BD el nombre guardado ES el nombre del niño (nombre_nino),
// así que lo usamos para ambas secciones de la página.
$nino = [
    'nombre'          => $fila['nombre_nino'],
    'foto'            => 'img/avatar-nino.png',  // ← avatar del niño
    'estrellas'       => $estrellas,
    'musica'          => true,   // puedes agregar columna "musica" a la tabla usuarios
    'efectos'         => true,   // puedes agregar columna "efectos"
    'narracion'       => true,   // puedes agregar columna "narracion"
    'notificaciones'  => true,   // puedes agregar columna "notificaciones"
];
 
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Configuración</title>
 
  <!-- Google Fonts: Nunito (redondeada, amigable para niños) -->
  <link href="https://fonts.googleapis.com/css2?family=Balsamiq+Sans:wght@700&family=Fredoka:wght@600;900&family=Nunito:wght@700;900&family=Quicksand:wght@500;700;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles/configuracion.css" />
</head>
<body>
 
<!-- ══════════════════════════════════════════════════
     BARRA DE NAVEGACIÓN SUPERIOR
     Imágenes necesarias:
       img/logo-leo-friends.png   → logo del koala en la esquina
       img/icono-lectura.png      → ícono "Lectura con Leo"
       img/icono-gramatica.png    → ícono "Gramática con Capy"
       img/icono-biblioteca.png   → ícono "Biblioteca con Fina"
       img/avatar-nino.png        → foto redonda del niño (topbar)
══════════════════════════════════════════════════ -->
<nav class="topbar">
  <a href="index.php" class="topbar__logo">
    <img src="images/cartelito.png" alt="Leo & Friends" />
    <span class="topbar__logo-text">Leo &amp;<br>Friends</span>
  </a>
 
  <div class="topbar__nav">
    <a href="lectura.php">
      <img src="images/Leito.png" alt="" />
      <span>Lectura con Leo</span>
    </a>
    <a href="gramatica.php">
      <img src="images/capy1.png" alt="" />
      <span>Gramática con Capy</span>
    </a>
    <a href="biblioteca.php">
      <img src="images/finxito3.png" alt="" />
      <span>Biblioteca con Finx</span>
    </a>
  </div>
 
  <div class="topbar__user">
    <span><?= htmlspecialchars($nino['nombre']) ?></span>
    <div class="topbar__stars"><?= (int)$nino['estrellas'] ?></div>
    <img class="avatar" src="img/avatar-nino.png" alt="Avatar niño" />
    <span class="topbar__gear">⚙</span>
  </div>
</nav>
 
 
<!-- ══════════════════════════════════════════════════
     CONTENIDO PRINCIPAL
══════════════════════════════════════════════════ -->
<main class="main">
 
  <h1 class="page-title">Configuración</h1>
 
  <!-- ─────────────────────────────────────────────
       SECCIÓN 1: MI CUENTA  (colapsable ▼)
  ───────────────────────────────────────────── -->
  <div class="seccion">
    <p class="seccion__titulo">1. Mi cuenta</p>
 
    <details class="accordion" open>
      <summary>
        Mi cuenta
        <span class="flecha">▼</span>
      </summary>
 
      <div class="accordion__body">
 
        <!-- Fila: foto + nombre + botón Editar -->
        <!--
          IMAGEN: img/avatar-mama.png
          Foto de perfil del adulto (padre/tutor). Tamaño recomendado: 100×100 px,
          recortada en círculo por CSS.
        -->
        <div class="perfil-fila">
          <img class="avatar-lg"
               src="img/avatar-mama.png"
               alt="Foto de <?= htmlspecialchars($usuario['nombre']) ?>" />
          <div class="perfil-fila__datos">
            <div class="perfil-fila__nombre"><?= htmlspecialchars($usuario['nombre']) ?></div>
            <div class="perfil-fila__correo"><?= htmlspecialchars($usuario['correo']) ?></div>
          </div>
          <a href="editar-cuenta.php" class="btn btn--outline-verde">✏ Editar</a>
        </div>
 
        <!-- Fila: Cambiar contraseña -->
        <!--
          IMAGEN: img/icono-candado.png
          Ícono de candado (🔒). Tamaño sugerido: 28×28 px.
        -->
        <div class="fila">
          <div class="fila__icono">
            <img src="img/icono-candado.png" alt="" />
          </div>
          <div class="fila__info">
            <div class="fila__label">Cambiar contraseña</div>
          </div>
          <div class="fila__derecha">
            <a href="cambiar-contrasena.php" class="arrow-link">›</a>
          </div>
        </div>
 
        <!-- Fila: Idioma -->
        <!--
          IMAGEN: img/icono-idioma.png
          Ícono de globo/idioma (🌐). Tamaño sugerido: 28×28 px.
        -->
        <div class="fila">
          <div class="fila__icono">
            <img src="img/icono-idioma.png" alt="" />
          </div>
          <div class="fila__info">
            <div class="fila__label">Idioma</div>
          </div>
          <div class="fila__derecha">
            <select class="select-idioma" name="idioma" onchange="this.form && this.form.submit()">
              <option value="es" <?= $usuario['idioma']==='Español' ? 'selected' : '' ?>>Español</option>
              <option value="en" <?= $usuario['idioma']==='English' ? 'selected' : '' ?>>English</option>
            </select>
          </div>
        </div>
 
        <!-- Fila: Plan actual -->
        <!--
          IMAGEN: img/icono-corona.png
          Ícono de corona (👑) representando el plan premium. Tamaño: 28×28 px.
        -->
        <div class="fila">
          <div class="fila__icono">
            <img src="img/icono-corona.png" alt="" />
          </div>
          <div class="fila__info">
            <div class="fila__label">Plan actual</div>
            <div class="fila__sub"><?= htmlspecialchars($usuario['plan']) ?></div>
          </div>
          <div class="fila__derecha">
            <a href="gestionar-plan.php" class="btn btn--outline-verde">Gestionar plan</a>
          </div>
        </div>
 
      </div><!-- /.accordion__body -->
    </details>
  </div><!-- /.seccion -->
 
 
  <!-- ─────────────────────────────────────────────
       SECCIÓN 2: CONFIGURACIÓN DEL NIÑO  (colapsable ▼)
  ───────────────────────────────────────────── -->
  <div class="seccion">
    <p class="seccion__titulo">2. Configuración del niño</p>
 
    <details class="accordion" open>
      <summary>
        Configuración del niño
        <span class="flecha">▼</span>
      </summary>
 
      <div class="accordion__body">
 
        <!-- Perfil del niño -->
        <!--
          IMAGEN: img/avatar-nino.png
          Avatar/ilustración del niño. Puede ser un personaje caricaturesco.
          Tamaño recomendado: 100×100 px.
        -->
        <div class="perfil-fila">
          <img class="avatar-lg"
               src="img/avatar-nino.png"
               alt="Avatar de <?= htmlspecialchars($nino['nombre']) ?>" />
          <div class="perfil-fila__datos">
            <div class="perfil-fila__nombre"><?= htmlspecialchars($nino['nombre']) ?></div>
            <div class="perfil-fila__correo">⭐ <?= (int)$nino['estrellas'] ?> estrellas</div>
          </div>
          <a href="editar-nino.php" class="btn btn--outline-verde">Editar</a>
        </div>
 
        <!-- Fila: Sonido (3 toggles: Música, Efectos, Narración) -->
        <!--
          IMAGEN: img/icono-sonido.png
          Ícono de bocina/altavoz (🔊). Tamaño: 28×28 px.
        -->
        <div class="fila">
          <div class="fila__icono">
            <img src="img/icono-sonido.png" alt="" />
          </div>
          <div class="fila__info">
            <div class="fila__label">Sonido</div>
          </div>
          <div class="fila__derecha">
            <div class="toggles-grupo">
 
              <!-- Toggle Música -->
              <div class="toggle-wrap">
                <span class="toggle-label">Música</span>
                <label class="toggle">
                  <input type="checkbox"
                         name="musica"
                         value="1"
                         <?= $nino['musica'] ? 'checked' : '' ?>
                         onchange="guardarAjuste('musica', this.checked)" />
                  <span class="toggle-slider"></span>
                </label>
              </div>
 
              <!-- Toggle Efectos -->
              <div class="toggle-wrap">
                <span class="toggle-label">Efectos</span>
                <label class="toggle">
                  <input type="checkbox"
                         name="efectos"
                         value="1"
                         <?= $nino['efectos'] ? 'checked' : '' ?>
                         onchange="guardarAjuste('efectos', this.checked)" />
                  <span class="toggle-slider"></span>
                </label>
              </div>
 
              <!-- Toggle Narración -->
              <div class="toggle-wrap">
                <span class="toggle-label">Narración</span>
                <label class="toggle">
                  <input type="checkbox"
                         name="narracion"
                         value="1"
                         <?= $nino['narracion'] ? 'checked' : '' ?>
                         onchange="guardarAjuste('narracion', this.checked)" />
                  <span class="toggle-slider"></span>
                </label>
              </div>
 
            </div>
          </div>
        </div>
 
        <!-- Fila: Notificaciones -->
        <!--
          IMAGEN: img/icono-campana.png
          Ícono de campana de notificación (🔔). Tamaño: 28×28 px.
        -->
        <div class="fila">
          <div class="fila__icono">
            <img src="img/icono-campana.png" alt="" />
          </div>
          <div class="fila__info">
            <div class="fila__label">Notificaciones</div>
            <div class="fila__sub">Recordatorios diarios de práctica</div>
          </div>
          <div class="fila__derecha">
            <label class="toggle">
              <input type="checkbox"
                     name="notificaciones"
                     value="1"
                     <?= $nino['notificaciones'] ? 'checked' : '' ?>
                     onchange="guardarAjuste('notificaciones', this.checked)" />
              <span class="toggle-slider"></span>
            </label>
          </div>
        </div>
 
      </div><!-- /.accordion__body -->
    </details>
  </div><!-- /.seccion -->
 
 
  <!-- ─────────────────────────────────────────────
       SECCIÓN 3: CUENTA  (colapsable ▼)
  ───────────────────────────────────────────── -->
  <div class="seccion">
    <p class="seccion__titulo">3. Cuenta</p>
 
    <details class="accordion">
      <summary>
        Cuenta
        <span class="flecha">▼</span>
      </summary>
 
      <div class="accordion__body">
 
        <!-- Cerrar sesión -->
        <!--
          IMAGEN: img/icono-salir.png
          Ícono de salida/puerta (→□). Color rojo. Tamaño: 24×24 px.
        -->
        <form method="POST" action="logout.php">
          <button type="submit" class="btn--rojo">
            <img src="img/icono-salir.png" alt="" style="width:22px;height:22px;" />
            Cerrar sesión
          </button>
        </form>
 
      </div>
    </details>
  </div><!-- /.seccion -->
 
 
  <p class="footer-version">Leo &amp; Friends v2.1.0</p>
 
</main><!-- /.main -->
 
 
<!-- Script separado: maneja el guardado AJAX de los toggles -->
<script src="js/configuracion.js"></script>
 
</body>
</html>