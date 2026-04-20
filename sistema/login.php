<?php
session_start(); // Iniciamos la sesión

$usuario = $_POST['usuario'];
$clave = $_POST['clave'];

$archivos = scandir('../db/usuario');
$encontrado = false;

foreach ($archivos as $archivo) {
    if ($archivo !== '.' && $archivo !== '..') {
        $contenido = file_get_contents('../db/usuario/' . $archivo);
        $usuarioJson = json_decode($contenido, true);

        if (!$usuarioJson)
            continue;

        if ($usuarioJson['activo'] != 1)
            continue; // Verificamos que el usuario esté activo

        if ($usuario === $usuarioJson['usuario'] && password_verify($clave, $usuarioJson['clave'])) {
            $encontrado = true;

            // Guardamos los datos del usuario en la sesión
            $_SESSION['usuario_id'] = $usuarioJson['id'];
            $_SESSION['usuario_nombre'] = $usuarioJson['nombre'];
            $_SESSION['usuario_usuario'] = $usuarioJson['usuario'];
            $_SESSION['logueado'] = true;
            break;
        }
    }
}

header('Content-Type: application/json');

if ($encontrado) {
    echo json_encode(["code" => 200, "msg" => $usuarioJson['nombre']]);
} else {
    echo json_encode(["code" => 401, "msg" => "Usuario o clave incorrectos"]);
}
?>