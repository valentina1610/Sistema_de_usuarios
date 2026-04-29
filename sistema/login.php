<?php
session_start();

$usuario = $_POST['usuario'];
$clave   = $_POST['clave'];

$pathUsuarios = '../db/usuario';
$pathPermisos = '../db/permisos';

$archivos   = scandir($pathUsuarios);
$encontrado = false;

$modulos = [
    'General', 'Paciente', 'Clientes', 'Agenda', 'Productos',
    'Ordenes de Servicio', 'Facturacion', 'Libros', 'Proveedores',
    'Usuarios', 'Roles', 'Configuraciones',
];

foreach ($archivos as $archivo) {
    if ($archivo === '.' || $archivo === '..') continue;

    $contenido   = file_get_contents($pathUsuarios . '/' . $archivo);
    $usuarioJson = json_decode($contenido, true);

    if (!$usuarioJson) continue;
    if ($usuarioJson['activo'] != 1) continue;

    if ($usuario === $usuarioJson['usuario'] && password_verify($clave, $usuarioJson['clave'])) {
        $encontrado = true;

        $_SESSION['usuario_id']      = $usuarioJson['id'];
        $_SESSION['usuario_nombre']  = $usuarioJson['nombre'];
        $_SESSION['usuario_usuario'] = $usuarioJson['usuario'];
        $_SESSION['logueado']        = true;

        // Owner (id == 1): acceso total e inmutable
        if ($usuarioJson['id'] == 1) {
            $permisosResueltos = [];
            foreach ($modulos as $m) $permisosResueltos[$m] = 1;
            $_SESSION['permiso_id']          = 'owner';
            $_SESSION['permiso_descripcion'] = 'Owner';
            $_SESSION['es_owner']            = true;
            $_SESSION['permisos']            = $permisosResueltos;

        } else {
            $_SESSION['es_owner']   = false;
            $permiso_id             = $usuarioJson['permiso_id'] ?? null;
            $_SESSION['permiso_id'] = $permiso_id;

            if ($permiso_id && file_exists($pathPermisos . '/' . $permiso_id . '.json')) {
                $permisoData = json_decode(file_get_contents($pathPermisos . '/' . $permiso_id . '.json'), true);
                $_SESSION['permisos']            = $permisoData['permisos']     ?? [];
                $_SESSION['permiso_descripcion'] = $permisoData['descripcion']  ?? $permiso_id;
            } else {
                $permisosResueltos = [];
                foreach ($modulos as $m) $permisosResueltos[$m] = 0;
                $_SESSION['permisos']            = $permisosResueltos;
                $_SESSION['permiso_descripcion'] = null;
            }
        }

        break;
    }
}

header('Content-Type: application/json');

if ($encontrado) {
    echo json_encode(["code" => 200, "msg" => $usuarioJson['nombre']]);
} else {
    echo json_encode(["code" => 401, "msg" => "Usuario o clave incorrectos"]);
}
?>