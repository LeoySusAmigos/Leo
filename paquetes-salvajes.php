<?php
session_start();
include("php/conexion.php");

// ── Protección: solo usuarios con sesión ───────────────
if (!isset($_SESSION['userID'])) {
    header("Location: login.html");
    exit();
}

$userID = $_SESSION['userID'];

// ── Traer paquetes y sus beneficios de la BD ───────────
$sqlPaquetes = "SELECT * FROM paquetes WHERE activo = 1 ORDER BY precio ASC";
$resPaquetes = $conn->query($sqlPaquetes);
$paquetes = [];
while ($p = $resPaquetes->fetch_assoc()) {
    $paquetes[] = $p;
}

// Traer beneficios de cada paquete
foreach ($paquetes as &$p) {
    $id  = $p['paqueteID'];
    $res = $conn->query("SELECT descripcion FROM paquete_beneficios
                         WHERE paqueteID = $id ORDER BY orden ASC");
    $p['beneficios'] = [];
    while ($b = $res->fetch_assoc()) {
        $p['beneficios'][] = $b['descripcion'];
    }
}
unset($p);

// ── Suscripción actual del usuario ─────────────────────
$sqlSub = "SELECT s.*, p.nombre AS nombre_paquete
           FROM suscripciones s
           JOIN paquetes p ON s.paqueteID = p.paqueteID
           WHERE s.userID = $userID AND s.estado = 'activa'
           LIMIT 1";
$resSub = $conn->query($sqlSub);
$suscripcionActual = $resSub->num_rows > 0 ? $resSub->fetch_assoc() : null;

// ── Colores y mascotas por paquete
// paqueteID 1 = Plan Aventura (Capy)
// paqueteID 2 = Plan Safari   (Leo + Capy + Finx)
// paqueteID 3 = Plan Gratis   (Leo)
$estilos = [
    1 => [  // Plan Gratis (Leo)
        'color'       => '#2d9e4e',
        'color_btn'   => '#1a6e2e',
        'color_light' => '#e8f7ec',
        'mascotas'    => ['images/Leito.png'],
        'ancho'       => ['90px'],
    ],
    2 => [  // Plan Aventura (Capy)
        'color'       => '#1e88e5',
        'color_btn'   => '#1565c0',
        'color_light' => '#e3f2fd',
        'mascotas'    => ['images/capy1.png'],
        'ancho'       => ['90px'],
    ],
    3 => [  // Plan Safari (Leo + Capy + Finx)
        'color'       => '#ff7a00',
        'color_btn'   => '#cc6200',
        'color_light' => '#fff3e8',
        'mascotas'    => ['images/Leito.png', 'images/capy1.png', 'images/FinxHi.png'],
        'ancho'       => ['60px', '70px', '60px'],
    ],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paquetes Salvajes — Leo & Friends</title>
    <link rel="shortcut icon" href="images/favicon/favicon-32x32.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        /* ── Reset ── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        /* ── Fondo selva ── */
        body {
            font-family: 'Nunito', sans-serif;
            min-height: 100vh;
            background:
                linear-gradient(rgba(0,0,0,.18), rgba(0,0,0,.18)),
                url('images/configuracion_fondo.png') center/cover fixed;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 48px 16px 64px;
        }

        /* ── Encabezado ── */
        .encabezado {
            text-align: center;
            margin-bottom: 40px;
        }

        .encabezado h1 {
            font-family: 'Fredoka', sans-serif;
            font-size: 3rem;
            font-weight: 700;
            color: #fff;
            text-shadow: 0 3px 12px rgba(0,0,0,.35);
            margin-bottom: 10px;
        }

        .encabezado p {
            font-size: 1.1rem;
            color: rgba(255,255,255,.9);
            font-weight: 600;
            text-shadow: 0 2px 6px rgba(0,0,0,.3);
            max-width: 500px;
            margin: 0 auto;
        }

        /* ── Alerta de plan actual ── */
        .alerta-plan {
            background: rgba(255,255,255,.92);
            border-radius: 14px;
            padding: 12px 24px;
            font-weight: 700;
            font-size: .95rem;
            color: #1a6e2e;
            margin-bottom: 32px;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 14px rgba(0,0,0,.12);
        }

        /* ── Grid de tarjetas ── */
        .tarjetas {
            display: flex;
            gap: 24px;
            justify-content: center;
            flex-wrap: wrap;
            width: 100%;
            max-width: 1200px;  /* antes: 880px — no alcanzaba para 3 tarjetas */
        }

        /* ── Tarjeta individual ── */
        .tarjeta {
            background: #fff;
            border-radius: 24px;
            padding: 36px 28px 32px;
            width: calc(33.333% - 16px);  /* 3 en fila con el gap */
            min-width: 260px;              /* mínimo antes de que se apilen */
            max-width: 340px;
            box-shadow: 0 12px 40px rgba(0,0,0,.18);
            display: flex;
            flex-direction: column;
            position: relative;
            transition: transform .3s ease, box-shadow .3s ease;
        }

        .tarjeta:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 50px rgba(0,0,0,.22);
        }

        /* Tarjeta popular (Plan Safari) */
        .tarjeta.popular {
            border: 3px solid #ff7a00;
        }

        /* Badge "Más popular" */
        .badge-popular {
            position: absolute;
            top: -14px;
            left: 50%;
            transform: translateX(-50%);
            background: #ff7a00;
            color: #fff;
            font-family: 'Fredoka', sans-serif;
            font-size: .95rem;
            font-weight: 600;
            padding: 4px 20px;
            border-radius: 20px;
            white-space: nowrap;
            box-shadow: 0 4px 10px rgba(255,122,0,.35);
        }

        /* ── Mascota flotando arriba de la tarjeta ── */
        .mascota-tarjeta {
            width: 90px;
            margin: 0 auto 12px;
            display: block;
            filter: drop-shadow(0 6px 10px rgba(0,0,0,.2));
            animation: flotar 3s ease-in-out infinite;
        }

        @keyframes flotar {
            0%, 100% { transform: translateY(0);    }
            50%       { transform: translateY(-8px); }
        }

        /* ── Nombre del plan ── */
        .tarjeta__nombre {
            font-family: 'Fredoka', sans-serif;
            font-size: 1.7rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 6px;
        }

        /* ── Precio ── */
        .tarjeta__precio {
            text-align: center;
            margin-bottom: 20px;
        }

        .tarjeta__precio .monto {
            font-family: 'Fredoka', sans-serif;
            font-size: 3.2rem;
            font-weight: 700;
            line-height: 1;
        }

        .tarjeta__precio .periodo {
            font-size: .9rem;
            color: #888;
            font-weight: 600;
        }

        /* ── Descripción ── */
        .tarjeta__desc {
            font-size: .88rem;
            color: #666;
            text-align: center;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        /* ── Divisor ── */
        .tarjeta hr {
            border: none;
            border-top: 1px solid #eee;
            margin-bottom: 20px;
        }

        /* ── Lista de beneficios ── */
        .tarjeta__beneficios {
            list-style: none;
            flex: 1;
            margin-bottom: 28px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .tarjeta__beneficios li {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: .9rem;
            color: #333;
            line-height: 1.4;
        }

        .tarjeta__beneficios li .check {
            flex-shrink: 0;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .75rem;
            color: #fff;
            margin-top: 1px;
        }

        /* ── Botón seleccionar ── */
        .btn-seleccionar {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 14px;
            font-family: 'Fredoka', sans-serif;
            font-size: 1.15rem;
            font-weight: 600;
            color: #fff;
            cursor: pointer;
            transition: transform .15s, box-shadow .15s, opacity .15s;
        }

        .btn-seleccionar:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(0,0,0,.2);
        }

        .btn-seleccionar:active {
            transform: translateY(1px);
        }

        /* Botón deshabilitado (plan actual) */
        .btn-seleccionar:disabled {
            opacity: .55;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        /* ── Mensaje de éxito ── */
        .msg-exito {
            display: none;
            background: rgba(255,255,255,.95);
            color: #1a6e2e;
            font-weight: 700;
            border-radius: 14px;
            padding: 14px 24px;
            margin-top: 32px;
            font-size: 1rem;
            text-align: center;
            box-shadow: 0 4px 14px rgba(0,0,0,.12);
        }

        /* ── Botón volver ── */
        .btn-volver {
            margin-top: 36px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,.18);
            color: #fff;
            text-decoration: none;
            font-weight: 700;
            font-size: .95rem;
            padding: 10px 24px;
            border-radius: 20px;
            transition: background .2s;
        }

        .btn-volver:hover {
            background: rgba(255,255,255,.3);
            color: #fff;
        }

        /* ── Responsive ── */
        @media (max-width: 900px) {
            /* En tablet se apilan de 2 en 2 */
            .tarjeta {
                width: calc(50% - 12px);
                max-width: 100%;
            }
        }

        @media (max-width: 600px) {
            /* En móvil una sola columna */
            .tarjeta {
                width: 100%;
                max-width: 100%;
            }
            .encabezado h1 { font-size: 2.2rem; }
            .tarjetas { gap: 20px; }
        }

        @media (max-width: 425px) {
            body { padding: 32px 12px 48px; }
            .encabezado h1 { font-size: 1.8rem; }
            .encabezado p  { font-size: .95rem; }
            .tarjeta { padding: 28px 20px 24px; }
            .tarjeta__precio .monto { font-size: 2.6rem; }
        }
    </style>
</head>
<body>

    <!-- ── Encabezado ── -->
    <div class="encabezado">
        <h1>Paquetes Salvajes</h1>
        <p>Elige quién acompañará a tu pequeño en su aventura por la selva de la lectura.</p>
    </div>

    <!-- ── Plan actual del usuario ── -->
    <?php if ($suscripcionActual): ?>
        <div class="alerta-plan">
            <i class="fa-solid fa-circle-check"></i>
            Tu plan actual: <strong><?= htmlspecialchars($suscripcionActual['nombre_paquete']) ?></strong>
        </div>
    <?php endif; ?>

    <!-- ── Tarjetas ── -->
    <div class="tarjetas">

        <?php foreach ($paquetes as $paquete):
            $pid     = $paquete['paqueteID'];
            $estilo  = $estilos[$pid] ?? $estilos[1];
            $esActual = $suscripcionActual && $suscripcionActual['paqueteID'] == $pid;
        ?>

        <div class="tarjeta <?= $paquete['popular'] ? 'popular' : '' ?>">

            <?php if ($paquete['popular']): ?>
                <div class="badge-popular">Más completo</div>
            <?php endif; ?>

            <!-- Mascotas flotando (una o varias según el plan) -->
            <div style="display:flex;justify-content:center;align-items:flex-end;gap:4px;margin-bottom:12px;min-height:100px;">
                <?php foreach ($estilo['mascotas'] as $i => $mascota): ?>
                    <img src="<?= $mascota ?>"
                         alt="mascota"
                         style="width:<?= $estilo['ancho'][$i] ?>;filter:drop-shadow(0 6px 10px rgba(0,0,0,.2));animation:flotar <?= 2.8 + $i * 0.4 ?>s ease-in-out infinite;">
                <?php endforeach; ?>
            </div>

            <!-- Nombre -->
            <div class="tarjeta__nombre" style="color:<?= $estilo['color'] ?>">
                <?= htmlspecialchars($paquete['nombre']) ?>
            </div>

            <!-- Precio -->
            <div class="tarjeta__precio">
                <div class="monto" style="color:<?= $estilo['color'] ?>">
                    $<?= $paquete['precio'] == 0 ? '0' : number_format($paquete['precio'], 2) ?>
                </div>
                <div class="periodo"><?= $paquete['precio'] == 0 ? 'gratis' : '/mes' ?></div>
            </div>

            <!-- Descripción -->
            <p class="tarjeta__desc"><?= htmlspecialchars($paquete['descripcion']) ?></p>

            <hr>

            <!-- Beneficios -->
            <ul class="tarjeta__beneficios">
                <?php foreach ($paquete['beneficios'] as $beneficio): ?>
                    <li>
                        <span class="check" style="background:<?= $estilo['color'] ?>">
                            <i class="fa-solid fa-check"></i>
                        </span>
                        <?= htmlspecialchars($beneficio) ?>
                    </li>
                <?php endforeach; ?>
            </ul>

            <!-- Botón seleccionar -->
            <button
                class="btn-seleccionar"
                style="background:<?= $estilo['color'] ?>; box-shadow: 0 5px 0 <?= $estilo['color_btn'] ?>;"
                onclick="seleccionarPaquete(<?= $pid ?>, this)"
                <?= $esActual ? 'disabled' : '' ?>>
                <?= $esActual ? '✓ Plan actual' : 'Seleccionar' ?>
            </button>

        </div>

        <?php endforeach; ?>

    </div>

    <!-- Mensaje de confirmación -->
    <div class="msg-exito" id="msgExito"></div>

    <!-- Volver a configuración -->
    <a onclick="window.history.back()" class="btn-volver">
        <i class="fa-solid fa-arrow-left"></i> Volver
    </a>

    <script>
    function seleccionarPaquete(paqueteID, btn) {

        // Deshabilitar todos los botones mientras se procesa
        document.querySelectorAll('.btn-seleccionar').forEach(b => b.disabled = true);
        btn.innerHTML = 'Guardando...';

        fetch('php/seleccionar-paquete.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ paqueteID: paqueteID })
        })
        .then(r => r.json())
        .then(data => {
            if (data.ok) {
                // Actualizar botones visualmente
                document.querySelectorAll('.btn-seleccionar').forEach(b => {
                    b.disabled = false;
                    b.textContent = 'Seleccionar';
                });
                btn.disabled  = true;
                btn.innerHTML = '✓ Plan actual';

                // Mostrar mensaje
                const msg = document.getElementById('msgExito');
                msg.style.display = 'block';
                msg.innerHTML = '¡Paquete actualizado a <strong>' + data.nombre + '</strong>!';

                // Scroll suave al mensaje
                msg.scrollIntoView({ behavior: 'smooth', block: 'center' });

            } else {
                alert('Error: ' + (data.msg || 'No se pudo cambiar el paquete.'));
                document.querySelectorAll('.btn-seleccionar').forEach(b => b.disabled = false);
                btn.innerHTML = 'Seleccionar';
            }
        })
        .catch(() => {
            alert('Error de conexión. Intenta de nuevo.');
            document.querySelectorAll('.btn-seleccionar').forEach(b => b.disabled = false);
            btn.innerHTML = 'Seleccionar';
        });
    }
    </script>

</body>
</html>