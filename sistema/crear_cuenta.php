<?php
include('include/usuario/usuario.php');

header('Content-Type: application/json');

$nombre          = $_POST['nombre'];
$usuario         = $_POST['usuario'];
$clave           = $_POST['clave'];
$confirmarClave  = $_POST['confirmarClave'];

if ($clave !== $confirmarClave) {
    echo json_encode(["ok" => false, "mensaje" => "Las contraseñas no coinciden"]);
    exit;
}

if (strlen($clave) < 6) {
    echo json_encode(["ok" => false, "mensaje" => "La contraseña debe tener al menos 6 caracteres"]);
    exit;
}

$claveHash = password_hash($clave, PASSWORD_DEFAULT);

$path = dirname(__DIR__) . '/db/usuario';
if (!is_dir($path))
    mkdir($path, 0777, true);

$archivos = scandir($path);
foreach ($archivos as $archivo) {
    if ($archivo === '.' || $archivo === '..') continue;
    $contenido   = file_get_contents($path . '/' . $archivo);
    $usuarioJson = json_decode($contenido, true);
    if (!$usuarioJson) continue;
    if ($usuario === $usuarioJson['usuario']) {
        echo json_encode(["ok" => false, "mensaje" => "El usuario ya existe, probá con otro"]);
        exit;
    }
}

$usuarioObj = new Usuario();
$usuarioObj->setNombre($nombre);
$usuarioObj->setUsuario($usuario);
$usuarioObj->setClave($claveHash);
$usuarioObj->setActivo(1);
$usuarioObj->setPermisoId('c88f3132-5d33-4b54-9a33-dff023bd0709'); // Usuario normal

$usuarioObj->guardar();

echo json_encode(["ok" => true, "mensaje" => "Usuario creado correctamente"]);
?>