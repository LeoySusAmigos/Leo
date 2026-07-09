<?php
header('Content-Type: application/json; charset=utf-8');

include("conexion.php");

$conn->set_charset("utf8mb4");

$sql_preguntas = "SELECT pregunta_id, texto_pregunta, tipo_accion FROM cuestionario_preguntas ORDER BY pregunta_id ASC";
$res_preguntas = $conn->query($sql_preguntas);

$preguntas = [];

if ($res_preguntas && $res_preguntas->num_rows > 0) {
    while ($pregunta = $res_preguntas->fetch_assoc()) {
        $pregunta_id = $pregunta['pregunta_id'];

        $item = [
            'pregunta_id' => intval($pregunta_id),
            'tipo_accion' => $pregunta['tipo_accion'],
            'subtitulo' => obtenerSubtituloPorId($pregunta_id), 
            'texto_pregunta' => $pregunta['texto_pregunta'],
            'opciones' => []
        ];

        $sql_opciones = "SELECT opcion_id, texto_opcion FROM cuestionario_opciones WHERE pregunta_id = $pregunta_id ORDER BY opcion_id ASC";
        $res_opciones = $conn->query($sql_opciones);
        
        if ($res_opciones) {
            while ($opcion = $res_opciones->fetch_assoc()) {
                $item['opciones'][] = [
                    'opcion_id' => intval($opcion['opcion_id']),
                    'texto_opcion' => $opcion['texto_opcion']
                ];
            }
        }

        $preguntas[] = $item;
    }
}

echo json_encode($preguntas, JSON_UNESCAPED_UNICODE);

$conn->close();

function obtenerSubtituloPorId($id) {
    switch ($id) {
        case 1: return "Primero, una pregunta para mamá o papá";
        case 2: return "Conozcamos sus gustos";
        case 3: return "Queremos acompañarlo con mucha empatía";
        case 4: return "Su lectura debe ser un espacio feliz";
        case 5: return "¡Última pregunta!";
        default: return "Personaliza tu experiencia";
    }
}
?>