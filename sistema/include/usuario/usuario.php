<?php
class Usuario
{
    private $id;
    private $nombre;
    private $usuario;
    private $clave;
    private $activo;
    private $permiso_id;

    public function setNombre($nombre)       { $this->nombre     = $nombre; }
    public function setUsuario($usuario)     { $this->usuario    = $usuario; }
    public function setClave($clave)         { $this->clave      = $clave; }
    public function setActivo($activo)       { $this->activo     = $activo; }
    public function setPermisoId($permiso_id){ $this->permiso_id = $permiso_id; }

    public function getId()        { return $this->id; }
    public function getNombre()    { return $this->nombre; }
    public function getUsuario()   { return $this->usuario; }
    public function getClave()     { return $this->clave; }
    public function getActivo()    { return $this->activo; }
    public function getPermisoId() { return $this->permiso_id; }

    public function guardar()
    {
        $path = dirname(__DIR__, 3) . '/db/usuario';

        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        $archivos = scandir($path);
        $count    = count($archivos) - 2;
        $this->id = $count + 1;

        $usuarioData = [
            'id'         => $this->id,
            'nombre'     => $this->nombre,
            'usuario'    => $this->usuario,
            'clave'      => $this->clave,
            'activo'     => $this->activo,
            'permiso_id' => $this->permiso_id ?? null,
        ];

        file_put_contents(
            $path . '/' . $this->id . '.json',
            json_encode($usuarioData, JSON_PRETTY_PRINT)
        );
    }
}
?>