<?php
header('Content-Type: application/json');

$campo = $_POST['campo'];
$busqueda = $_POST['busqueda'];

// Validamos que el campo sea uno permitido para evitar busquedas maliciosas
$camposPermitidos = ['nombre', 'usuario', 'id'];
if (!in_array($campo, $camposPermitidos)) {
    echo json_encode(["code" => 400, "msj" => "Campo no válido"]);
    exit;
}

$path = dirname(__DIR__, 3) . '/db/usuario';
$archivos = scandir($path);
$resultados = [];

foreach ($archivos as $archivo) {
    if ($archivo !== '.' && $archivo !== '..') {
        $contenido = file_get_contents($path . '/' . $archivo);
        $usuarioJson = json_decode($contenido, true);

        if (!$usuarioJson)
            continue;

        if ($usuarioJson[$campo] == $busqueda) {
            $resultados[] = $usuarioJson;
        }
    }
}

if (count($resultados) > 0) {
    echo json_encode(["code" => 200, "resultados" => $resultados]);
} else {
    echo json_encode(["code" => 201, "msj" => "No se encontraron resultados"]);
}
?>