<?php
require_once "Permisos.php";

class RepositorioPermisos
{
    private $path;

    public function __construct()
    {
        $this->path = dirname(__DIR__, 3) . "/db/permisos";

        if (!is_dir($this->path)) {
            mkdir($this->path, 0777, true);
        }
    }

    /**
     * Devuelve todos los permisos como array de arrays asociativos.
     */
    public function obtenerTodos()
    {
        $lista = [];
        $archivos = glob($this->path . "/*.json");

        if (!$archivos) return $lista;

        foreach ($archivos as $archivo) {
            $data = json_decode(file_get_contents($archivo), true);
            if ($data) {
                $lista[] = $data;
            }
        }

        return $lista;
    }

    /**
     * Guarda un permiso nuevo. Genera UUID y lo usa como nombre de archivo.
     */
    public function guardar(Permisos $permiso)
    {
        $uuid = $permiso->generarUUID();
        $permiso->setId($uuid);

        $data = $permiso->toArray();
        $rutaArchivo = $this->path . "/" . $uuid . ".json";

        file_put_contents($rutaArchivo, json_encode($data, JSON_PRETTY_PRINT));

        return $uuid;
    }

    /**
     * Elimina un permiso por su UUID.
     */
    public function eliminar($id)
    {
        $rutaArchivo = $this->path . "/" . $id . ".json";

        if (file_exists($rutaArchivo)) {
            unlink($rutaArchivo);
            return true;
        }

        return false;
    }
}