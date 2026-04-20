<?php
include('include/usuario/usuario.php');

header('Content-Type: application/json');

$nombre = $_POST['nombre'];
$usuario = $_POST['usuario'];
$clave = $_POST['clave'];
$confirmarClave = $_POST['confirmarClave'];

// Validamos que las claves coincidan
if ($clave !== $confirmarClave) {
    echo json_encode(["ok" => false, "mensaje" => "Las contraseñas no coinciden"]);
    exit;
}

// Validamos longitud mínima de clave
if (strlen($clave) < 6) {
    echo json_encode(["ok" => false, "mensaje" => "La contraseña debe tener al menos 6 caracteres"]);
    exit;
}

$claveHash = password_hash($clave, PASSWORD_DEFAULT);

// Verificamos si el usuario ya existe
$path = dirname(__DIR__) . '/db/usuario';
if (!is_dir($path))
    mkdir($path, 0777, true); // Creamos el directorio si no existe

$archivos = scandir($path);
foreach ($archivos as $archivo) {
    if ($archivo !== '.' && $archivo !== '..') {
        $contenido = file_get_contents($path . '/' . $archivo);
        $usuarioJson = json_decode($contenido, true);
        if (!$usuarioJson)
            continue;
        if ($usuario === $usuarioJson['usuario']) {
            echo json_encode(["ok" => false, "mensaje" => "El usuario ya existe, probá con otro"]);
            exit;
        }
    }
}

// Creamos el usuario
$usuarioObj = new Usuario();
$usuarioObj->setNombre($nombre);
$usuarioObj->setUsuario($usuario);
$usuarioObj->setClave($claveHash);
$usuarioObj->setActivo(1);
$usuarioObj->guardar();

echo json_encode(["ok" => true, "mensaje" => "Usuario creado correctamente"]);
?>