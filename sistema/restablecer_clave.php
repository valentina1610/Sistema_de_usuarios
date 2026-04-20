<?php
header('Content-Type: application/json');

$usuario = $_POST['usuario'];
$path = dirname(__DIR__) . '/db/usuario';
$archivos = scandir($path);
$encontrado = false;

foreach ($archivos as $archivo) {
    if ($archivo !== '.' && $archivo !== '..') {
        $contenido = file_get_contents($path . '/' . $archivo);
        $usuarioJson = json_decode($contenido, true);
        if (!$usuarioJson)
            continue;

        if ($usuario === $usuarioJson['usuario']) {
            $encontrado = true;
            break;
        }
    }
}

if ($encontrado) {
    $nuevaClave = rand(100000, 999999); // Generamos una nueva clave aleatoria de 6 dígitos
    $usuarioJson['clave'] = password_hash((string) $nuevaClave, PASSWORD_DEFAULT); // Hasheamos la nueva clave
    file_put_contents($path . '/' . $archivo, json_encode($usuarioJson, JSON_PRETTY_PRINT));

    // Devolvemos la nueva clave al frontend para mostrarla al usuario
    echo json_encode(["ok" => true, "nuevaClave" => $nuevaClave]);
} else {
    echo json_encode(["ok" => false, "mensaje" => "Usuario no encontrado"]);
}
?>