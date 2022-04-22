<?php
/**
 * @version 2022-1
 * @author Martin Mata
 */

namespace Clases\MySql;

/**
 * Permite las funciones basicas de una conexion a mysql db
 */

class Conexion
{
    /** @var string */
    public $servidor = BD_SERVIDOR;
    
    /** @var string */
    public $usuario = BD_USUARIO;
    
    /** @var string */
    public $clave = BD_CLAVE;
    
    /** @var string */
    public $base_datos = "";
    
    /** @var \mysqli */
    public $conexion;
    
    /**
     * Cierra la conexion cuando se destruye la clase
     */
    public function __destruct()
    {
        //$this->conexion->close();
    }
    
    /**
     * Inicia la conexion
     * @param string $base_datos
     */
    public function __construct(string $base_datos)
    {
        $this->conexion = new \mysqli($this->servidor, $this->usuario, $this->clave, $base_datos);
        $this->base_datos = $base_datos;
    }
    
    /**
     *
     * @param string $base_datos
     * @return bool
     */
    public function conectado($base_datos = null)
    {
        if($base_datos !== null)
            $this->base_datos = $base_datos;
            return $this->conexion->real_connect($this->servidor, $this->usuario, $this->clave, $this->base_datos);
    }
    
    /**
     *
     * @param string $base_datos
     * @return bool;
     */
    public function selecionaBD($base_datos)
    {
        $this->base_datos = $base_datos;
        return $this->conexion->select_db($base_datos);
    }
    
    /**
     *
     * @param string $nombre
     * @return bool
     */
    public function crearBD($nombre)
    {
        $nombre = $this->conexion->real_escape_string($nombre);
        $query = "CREATE DATABASE IF NOT EXISTS $nombre DEFAULT CHARACTER SET utf8 COLLATE utf8_spanish_ci";
        return $this->conexion->query($query);
    }
    
    
    
}
?>