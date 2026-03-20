<?php
class usuario
{
    private $id;
    private $nombre;
    private $usuario;
    private $clave;
    private $activo;

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;
    }

    public function setClave($clave)
    {
        $this->clave = $clave;
    }

    public function setActivo($activo)
    {
        $this->activo = $activo;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getUsuario()
    {
        return $this->usuario;
    }

    public function getClave()
    {
        return $this->clave;
    }

    public function getActivo()
    {
        return $this->activo;
    }

    public function guardar()
    {
        $archivos = scandir('../db/usuario'); //obtenemos los archivos del directorio usuario
        $count = count($archivos) - 2; // contamos los archivos del directorio usuario y restamos los directorios padre e hijo
        $this->id = $count + 1; // asignamos un id al usuario basado en la cantidad de archivos en el directorio usuario
        $usuarioData = [ //creamos un array con los datos del usuario
            'id' => $this->id,
            'nombre' => $this->nombre,
            'usuario' => $this->usuario,
            'clave' => $this->clave,
            'activo' => 1
        ];
        $json = json_encode($usuarioData, JSON_PRETTY_PRINT); // convertimos el array a json
        file_put_contents('../db/usuario/' . $this->id . '.json', $json); // guardamos el json en el directorio usuario con el nombre del usuario



    }
}
?>