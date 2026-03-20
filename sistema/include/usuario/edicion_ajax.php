<?php
// Recibimos datos del formulario
$ID = $_POST['id'];
$nombre = $_POST['nombre'];
$usuario = $_POST['usuario'];
$clave = $_POST['clave'];
$activo = $_POST['activo'];

$path = dirname(__DIR__, 3) . '/db/usuario'; // Obtenemos la ruta al directorio de usuarios

$archivos = scandir($path); // Obtenemos los archivos del directorio de usuarios

foreach ($archivos as $archivo) { // Iteramos sobre los archivos para verificar si el usuario ya existe
    if ($archivo !== '.' && $archivo !== '..') {

        $contenido = file_get_contents($path . '/' . $archivo);
        $usuarioJson = json_decode($contenido, true);

        if (!$usuarioJson)
            continue;

        if ($usuarioJson['usuario'] === $usuario && $ID != $usuarioJson['id']) { // Verificamos si el usuario ya existe y no es el mismo que estamos editando
            header('Content-Type: application/json');
            header('Content-Disposition: inline');
            echo json_encode(["ok" => false, "mensaje" => "El usuario ya existe, prueba con otro"]);
            exit;
        }
    }
}

$usuarioData = [ // Creamos un array con los datos del usuario a guardar
    "id" => $ID,
    "nombre" => $nombre,
    "usuario" => $usuario,
    "clave" => $clave,
    "activo" => $activo
];
$rutaArchivo = $path . '/' . $ID . '.json'; // Definimos la ruta del archivo a guardar, usando el ID como nombre del archivo
file_put_contents($rutaArchivo, json_encode($usuarioData, JSON_PRETTY_PRINT)); // Guardamos el usuario en un archivo JSON

header('Content-Type: application/json');
header('Content-Disposition: inline');
echo json_encode(["ok" => true, "mensaje" => "Usuario editado/agregado correctamente"]);

?>