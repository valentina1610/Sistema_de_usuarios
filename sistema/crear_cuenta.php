<?php
include('include/usuario/usuario.php');

// Recibimos datos del formulario
$nombre = $_POST['nombre'];
$usuario = $_POST['usuario'];
$clave = $_POST['clave'];
$confirmarClave = $_POST['confirmarClave'];

// Validamos clave
if ($clave !== $confirmarClave) {
    echo "Las claves no coinciden";
    exit;
}

// Verificamos si el usuario ya existe
$archivos = scandir('../db/usuario');
foreach ($archivos as $archivo) {
    if ($archivo !== '.' && $archivo !== '..') {
        $contenido = file_get_contents('../db/usuario/' . $archivo);
        $usuarioJson = json_decode($contenido, true);
        if ($usuario === $usuarioJson['usuario']) {
            echo "El usuario ya existe";
            exit;
        }
    }
}

// Creamos el usuario y lo guardamos
$usuarioObj = new Usuario();
$usuarioObj->setNombre($nombre);
$usuarioObj->setUsuario($usuario);
$usuarioObj->setClave($clave);
$usuarioObj->setActivo(1);

$usuarioObj->guardar();

echo "Usuario creado correctamente";
?>