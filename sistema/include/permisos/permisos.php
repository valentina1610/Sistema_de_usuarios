<?php

class Permisos
{
    private $id;
    private $descripcion;
    private $permisos = [];

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    }

    public function setPermisos($permisos)
    {
        $this->permisos = $permisos;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getDescripcion()
    {
        return $this->descripcion;
    }

    public function getPermisos()
    {
        return $this->permisos;
    }

    public function generarUUID()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    public function toArray()
    {
        return [
            "id"          => $this->id,
            "descripcion" => $this->descripcion,
            "permisos"    => $this->permisos
        ];
    }
}