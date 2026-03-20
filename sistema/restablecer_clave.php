<?php
$usuario = $_POST['usuario']; //obtenemos el usuario del formulario
$archivos = scandir('../db/usuario'); //obtenemos los archivos del directorio usuario
$encontrado = false; // variable para verificar si el usuario fue encontrado

foreach ($archivos as $archivo) {
    if ($archivo !== '.' && $archivo !== '..') {
        $contenido = file_get_contents('../db/usuario/' . $archivo);
        $usuarioJson = json_decode($contenido, true);
        if (!$usuarioJson)
            continue;

        if ($usuario === $usuarioJson['usuario']) {
            $encontrado = true;
            break;
        }
    }
}
header('Content-Type: application/json');
if ($encontrado) {
    $nuevaClave = rand(100000, 999999); // Generamos una nueva clave aleatoria de 6 dígitos
    $usuarioJson['clave'] = $nuevaClave; // Actualizamos la clave del usuario
    file_put_contents('../db/usuario/' . $archivo, json_encode($usuarioJson, JSON_PRETTY_PRINT));
    echo json_encode(["ok" => true, "mensaje" => "Clave restablecida. Tu nueva clave es: " . $nuevaClave]);
} else {
    echo json_encode(["ok" => false, "mensaje" => "Usuario no encontrado"]);

}
?>