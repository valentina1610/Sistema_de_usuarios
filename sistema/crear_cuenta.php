<?php
var_dump($_POST); //recibimos los datos del formulario

$nombre = $_POST['nombre']; //obtenemos el nombre del formulario
$usuario = $_POST['usuario']; //obtenemos el usuario del formulario
$clave = $_POST['clave']; //    obtenemos la clave del formulario
$confirmarClave = $_POST['confirmarClave']; // obtenemos la confirmacion de clave del formulario

if ($clave !== $confirmarClave) { // verificamos que las claves coincidan
    echo "Las claves no coinciden";
    exit;
}


$usuarioData = [ //creamos un array con los datos del usuario
    'id' => 1,
    'nombre' => $nombre,
    'usuario' => $usuario,
    'clave' => $clave,
    'activo' => 1
];
$archivos = scandir('../db/usuario'); //obtenemos los archivos del directorio usuario
var_dump($archivos); // verificamos si el usuario ya existe
$json = json_encode($usuarioData, JSON_PRETTY_PRINT); // convertimos el array a json
file_put_contents('../db/usuario/1.json', $json); // guardamos el json en el directorio usuario con el nombre del usuario
?>