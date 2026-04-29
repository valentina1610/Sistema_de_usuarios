<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['logueado']) || !$_SESSION['logueado']) {
    http_response_code(401);
    echo json_encode(["ok" => false, "mensaje" => "No autorizado"]);
    exit;
}

$ID      = $_POST['id'];
$nombre  = $_POST['nombre'];
$usuario = $_POST['usuario'];
$activo  = $_POST['activo'];
$permiso_id = $_POST['permiso_id'] ?? null;

// Clave es opcional en edición (si viene vacía no se cambia)
$clave   = $_POST['clave'] ?? '';

$path    = dirname(__DIR__, 3) . '/db/usuario';
$rutaArchivo = $path . '/' . $ID . '.json';

if (!file_exists($rutaArchivo)) {
    echo json_encode(["ok" => false, "mensaje" => "Usuario no encontrado"]);
    exit;
}

$usuarioActual = json_decode(file_get_contents($rutaArchivo), true);

// Proteger al owner (id=1): no se puede cambiar su permiso_id ni desactivarlo
if ($usuarioActual['id'] == 1) {
    $permiso_id = 'owner'; // siempre owner
    $activo     = 1;       // siempre activo
}

// Verificar usuario duplicado
$archivos = scandir($path);
foreach ($archivos as $archivo) {
    if ($archivo === '.' || $archivo === '..') continue;
    $contenido   = file_get_contents($path . '/' . $archivo);
    $usuarioJson = json_decode($contenido, true);
    if (!$usuarioJson) continue;
    if ($usuarioJson['usuario'] === $usuario && $ID != $usuarioJson['id']) {
        echo json_encode(["ok" => false, "mensaje" => "El usuario ya existe, probá con otro"]);
        exit;
    }
}

// Construir datos actualizados
$usuarioData = [
    "id"         => (int)$ID,
    "nombre"     => $nombre,
    "usuario"    => $usuario,
    "clave"      => $usuarioActual['clave'], // Mantener clave actual por defecto
    "activo"     => (int)$activo,
    "permiso_id" => $permiso_id,
];

// Solo actualizar clave si se envió una nueva
if (!empty($clave)) {
    if (strlen($clave) < 6) {
        echo json_encode(["ok" => false, "mensaje" => "La contraseña debe tener al menos 6 caracteres"]);
        exit;
    }
    $usuarioData['clave'] = password_hash($clave, PASSWORD_DEFAULT);
}

file_put_contents($rutaArchivo, json_encode($usuarioData, JSON_PRETTY_PRINT));

echo json_encode(["ok" => true, "mensaje" => "Usuario editado correctamente"]);
?>