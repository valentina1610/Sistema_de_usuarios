<?php
$usuario = $_POST['usuario']; //obtenemos el usuario del formulario
$clave = $_POST['clave']; //    obtenemos la clave del formulario

$archivos = scandir('../db/usuario'); //obtenemos los archivos del directorio usuario   
$encontrado = false; // variable para verificar si el usuario fue encontrado
foreach ($archivos as $archivo) {
    if ($archivo !== '.' && $archivo !== '..') {
        $contenido = file_get_contents('../db/usuario/' . $archivo);
        $usuarioJson = json_decode($contenido, true);
        if ($usuario === $usuarioJson['usuario'] && $clave === $usuarioJson['clave']) {
            $encontrado = true;
            break;
        }
    }
}
header('Content-Type: application/json'); // le dice al navegador: “lo que voy a devolver es JSON, no texto normal”
if ($encontrado) {
    echo json_encode(["ok" => true, "usuario" => $usuarioJson['nombre']]); // si el usuario es encontrado, devolvemos un json con el nombre del usuario
} else {
    echo json_encode(["ok" => false, "mensaje" => "Usuario o clave incorrectos"]); // si el usuario no es encontrado, devolvemos un json con un mensaje de error
}
?>