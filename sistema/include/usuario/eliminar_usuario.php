<?php
session_start();

if (!isset($_SESSION['logueado']) || !$_SESSION['logueado']) {
    http_response_code(401);
    echo json_encode(["error" => "No autorizado"]);
    exit;
}

header('Content-Type: application/json');

$ids  = $_POST['ids'] ?? [];
$path = dirname(__DIR__, 3) . '/db/usuario';

if (!is_array($ids) || count($ids) === 0) {
    http_response_code(400);
    echo json_encode(["error" => "No se enviaron IDs"]);
    exit;
}

$eliminados = 0;

foreach ($ids as $id) {
    // Proteger al owner (id=1): nunca se puede eliminar
    if ($id == 1) continue;

    $rutaArchivo = $path . '/' . $id . '.json';

    if (file_exists($rutaArchivo)) {
        unlink($rutaArchivo);
        $eliminados++;
    }
}

echo json_encode(["ok" => true, "eliminados" => $eliminados]);
?>