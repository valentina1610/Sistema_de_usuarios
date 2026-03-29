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
    echo json_encode(["code" => 200, "msg" => $usuarioJson['nombre']]); // si el usuario fue encontrado, devolvemos un código 200 y el nombre del usuario
} else {
    echo json_encode(["code" => 201, "msg" => "Usuario o clave incorrectos"]); // si el usuario no fue encontrado, devolvemos un código 201 y un mensaje de error
}
?>