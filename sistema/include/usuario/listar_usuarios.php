<?php
session_start();

if (!isset($_SESSION['logueado']) || !$_SESSION['logueado']) {
    http_response_code(401);
    echo json_encode(["error" => "No autorizado"]);
    exit;
}

header('Content-Type: application/json');

$path = dirname(__DIR__, 3) . '/db/usuario';

if (!is_dir($path)) {
    echo json_encode([]);
    exit;
}

$archivos  = scandir($path);
$usuarios  = [];

foreach ($archivos as $archivo) {
    if ($archivo === '.' || $archivo === '..') continue;

    $contenido = file_get_contents($path . '/' . $archivo);
    $data      = json_decode($contenido, true);

    if (!$data) continue;

    // No devolver la clave hasheada al frontend
    unset($data['clave']);

    $usuarios[] = $data;
}

// Ordenar por ID ascendente
usort($usuarios, fn($a, $b) => $a['id'] <=> $b['id']);

echo json_encode($usuarios);
?>