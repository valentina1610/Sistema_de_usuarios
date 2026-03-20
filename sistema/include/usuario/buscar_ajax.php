<?php

$campo = $_REQUEST['campo'];
$busqueda = $_REQUEST['busqueda'];

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

header('Content-Type: application/json');

if (count($resultados) > 0) {
    echo json_encode(["ok" => true, "resultados" => $resultados]);
} else {
    echo json_encode(["ok" => false, "mensaje" => "No se encontraron resultados"]);
}
?>