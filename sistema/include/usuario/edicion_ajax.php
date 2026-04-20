<?php
header('Content-Type: application/json');

$ID = $_POST['id'];
$nombre = $_POST['nombre'];
$usuario = $_POST['usuario'];
$clave = $_POST['clave'];
$activo = $_POST['activo'];

// Validamos longitud mínima de clave
if (strlen($clave) < 6) {
    echo json_encode(["ok" => false, "mensaje" => "La contraseña debe tener al menos 6 caracteres"]);
    exit;
}

$path = dirname(__DIR__, 3) . '/db/usuario';
$archivos = scandir($path);

foreach ($archivos as $archivo) {
    if ($archivo !== '.' && $archivo !== '..') {
        $contenido = file_get_contents($path . '/' . $archivo);
        $usuarioJson = json_decode($contenido, true);
        if (!$usuarioJson)
            continue;

        if ($usuarioJson['usuario'] === $usuario && $ID != $usuarioJson['id']) {
            echo json_encode(["ok" => false, "mensaje" => "El usuario ya existe, probá con otro"]);
            exit;
        }
    }
}

// Hasheamos la clave antes de guardar
$claveHash = password_hash($clave, PASSWORD_DEFAULT);

$usuarioData = [
    "id" => $ID,
    "nombre" => $nombre,
    "usuario" => $usuario,
    "clave" => $claveHash, // Guardamos la clave hasheada
    "activo" => $activo
];

$rutaArchivo = $path . '/' . $ID . '.json';
file_put_contents($rutaArchivo, json_encode($usuarioData, JSON_PRETTY_PRINT));

echo json_encode(["ok" => true, "mensaje" => "Usuario editado correctamente"]);
?>