<?php
include("conexion.php");

$libro_id = intval($_GET['id'] ?? 1);

$sql_nivel = "SELECT nivel_id FROM libros WHERE libro_id = $libro_id";
$res_nivel = mysqli_query($conn, $sql_nivel);
$libro     = mysqli_fetch_assoc($res_nivel);
$nivel     = $libro['nivel_id'] ?? 1;

$rangos = [
    1 => [4, 6],
    2 => [5, 8],
    3 => [6, 10],
    4 => [7, 12],
    5 => [8, 14],
];
$rango = $rangos[$nivel] ?? [5, 8];
$min   = $rango[0];
$max   = $rango[1];

$sql_paginas = "SELECT texto_pagina FROM paginas_libro WHERE libro_id = $libro_id";
$res_paginas = mysqli_query($conn, $sql_paginas);

$texto_completo = '';
while ($p = mysqli_fetch_assoc($res_paginas)) {
    $texto_completo .= ' ' . $p['texto_pagina'];
}

$oraciones_raw = preg_split('/(?<=[.!?])\s+/', trim($texto_completo));

$oraciones_validas = array_filter($oraciones_raw, function($or) use ($min, $max) {
    $c = str_word_count(trim($or), 0, 'áéíóúüñÁÉÍÓÚÜÑ');
    return $c >= $min && $c <= $max;
});

if (empty($oraciones_validas)) {
    $oraciones_validas = $oraciones_raw;
}

$oraciones_validas = array_values($oraciones_validas);
$fraseCompleta = rtrim(trim($oraciones_validas[array_rand($oraciones_validas)]), '.');

$palabras = explode(' ', $fraseCompleta);

$candidatas = array_filter($palabras, fn($p) => mb_strlen($p) > 3);
if (empty($candidatas)) $candidatas = $palabras;
$candidatas      = array_values($candidatas);
$palabraCorrecta = $candidatas[array_rand($candidatas)];

$longitud = mb_strlen($palabraCorrecta);
$inicial  = mb_strtoupper(mb_substr($palabraCorrecta, 0, 1));
$pista1   = "La palabra tiene $longitud letras.";
$pista2   = "La palabra empieza con la letra \"$inicial\".";

$todas_palabras = explode(' ', $texto_completo);
$distractores   = array_filter($todas_palabras, function($p) use ($palabraCorrecta) {
    $p = trim($p, '.,;:!?()"\'-');
    return mb_strlen($p) > 3 && strtolower($p) !== strtolower($palabraCorrecta);
});
$distractores = array_values(array_unique($distractores));
shuffle($distractores);

$ops      = [$palabraCorrecta];
$agregados = 0;
foreach ($distractores as $d) {
    $d = trim($d, '.,;:!?()"\'-');
    if ($agregados >= 2) break;
    $ops[] = $d;
    $agregados++;
}

$genericos = ['arbol', 'casa', 'perro', 'luna', 'libro', 'agua', 'nino', 'flor'];
while (count($ops) < 3) {
    $g = $genericos[array_rand($genericos)];
    if (!in_array($g, $ops)) $ops[] = $g;
}

$fila = [
    'pista1'        => $pista1,
    'pista2'        => $pista2,
    'opciones'      => $ops,
    'frase'         => $fraseCompleta,
];

$oracion = $palabraCorrecta;
?>