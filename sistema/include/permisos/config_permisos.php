<?php
session_start();

if (!isset($_SESSION['logueado']) || !$_SESSION['logueado']) {
    http_response_code(401);
    echo json_encode(["error" => "No autorizado"]);
    exit;
}

require_once __DIR__ . "/Permisos.php";
require_once __DIR__ . "/repo_permisos.php";

header('Content-Type: application/json');

$repo   = new RepositorioPermisos();
$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

// Lista de módulos del sistema (cada uno es un permiso con valor 0/1)
$modulos = [
    'General',
    'Paciente',
    'Clientes',
    'Agenda',
    'Productos',
    'Ordenes de Servicio',
    'Facturacion',
    'Libros',
    'Proveedores',
    'Usuarios',
    'Roles',
    'Configuraciones',
];

switch ($accion) { // Acciones: listar, crear, eliminar

    case 'listar': // Devuelve todos los permisos
        echo json_encode($repo->obtenerTodos());
        break;

    case 'crear':
        $descripcion = trim($_POST['descripcion'] ?? '');

        if ($descripcion === '') {
            http_response_code(400);
            echo json_encode(["error" => "La descripción es requerida"]);
            exit;
        }

        // Construir array de permisos: cada módulo en 0 por defecto
        $permisosArray = [];
        foreach ($modulos as $modulo) {
            $permisosArray[$modulo] = $modulo === 'General' ? 1 : 0;
        }

        $permiso = new Permisos();
        $permiso->setDescripcion($descripcion);
        $permiso->setPermisos($permisosArray);

        $uuid = $repo->guardar($permiso);

        echo json_encode([
            "ok"  => true,
            "id"  => $uuid,
            "permiso" => $permiso->toArray()
        ]);
        break;

    case 'eliminar':
        $ids = $_POST['ids'] ?? [];

        if (!is_array($ids) || count($ids) === 0) {
            http_response_code(400);
            echo json_encode(["error" => "No se enviaron IDs"]);
            exit;
        }

        $eliminados = 0;
        foreach ($ids as $id) {
            if ($repo->eliminar($id)) {
                $eliminados++;
            }
        }

        echo json_encode(["ok" => true, "eliminados" => $eliminados]);
        break;

    default:
        http_response_code(400);
        echo json_encode(["error" => "Acción no reconocida"]);
        break;
}