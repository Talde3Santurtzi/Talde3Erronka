<?php

    class Algo {
        
    private $nombre;
    private $apellido;
    public function setNombre($nombre)
    {
        return $this->nombre=$nombre;
    }
    public function getNombre()
    {
        return $this->nombre;
    }
    public function setApellido($apellido)
    {
        return $this->apellido=$apellido;
    }
    public function getApellido()
    {
        return $this->apellido;
    }
    }
?>