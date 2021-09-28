<?php
/**
 * @version v2021_1
 * @author Martin Mata
 */
namespace Clases\MySql;

/**
 * Permite las funciones basicas de una coneccion a mysql bd
 * Asume constantes definidas BD_SERVIDOR, BD_USUARIO, BD_CLAVE
 *
 * @author Martin
 *        
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
    public $base = "";

    public $conexion;

    /** @var bool */
    public $estaConectado = false;

    /** @var bool */
    public $esTransaccion = false;

    /** @var string */
    public $error_conexion = "NULL";

    /**
     * Cuando se destruye la clases es llamada esta funcion
     */
    public function __destruct()
    {
        $this->cerrar();
    }

    /**
     * inicia la coneccion
     *
     * @return object
     */
    public function conectar()
    {
        $this->conexion = @mysqli_connect($this->servidor, $this->usuario, $this->clave);
        if (mysqli_connect_errno())
        {
            $this->error_conexion = mysqli_connect_error();
            exit();
        }
        $this->estaConectado = true;
        @mysqli_query($this->conexion, "SET NAMES 'utf8'");
        return $this->conexion;
    }

    /**
     * Inicia la transaccion
     */
    function iniciaTransa()
    {
        @mysqli_autocommit($this->conexion, FALSE);
        $this->esTransaccion = true;
    }

    /**
     * Regresa el valor libre de mysql inyections
     *
     * @param string $cadena;
     * @return string
     */
    function escape($cadena)
    {
        if (is_array($cadena) || is_object($cadena))
        {
            foreach ($cadena as $key => $value)
            {
                $cadena[$key] = @mysqli_escape_string($this->conexion, $value);
            }
            return $cadena;
        }
        else
            return @mysqli_escape_string($this->conexion, $cadena);
    }

    /**
     * Termina la transaccion
     */
    function terminaTransa()
    {
        if ($this->esTransaccion == true)
        {
            @mysqli_commit($this->conexion);
            $this->esTransaccion = false;
        }
    }

    /**
     * Regresa la transaccion sin que se afecte ningun campo
     */
    function rollback()
    {
        if ($this->esTransaccion == true)
        {
            @mysqli_rollback($this->conexion);
            $this->esTransaccion = false;
        }
    }

    /**
     * Cierra la conexion
     */
    function cerrar()
    {
        if ($this->estaConectado == true)
        {
            @mysqli_close($this->conexion);
            $this->estaConectado = false;
        }
    }

    /**
     * Hace la consulta regresa el resultado
     *
     * @param string $query
     */
    function ejecutar($query)
    {
        return @mysqli_query($this->conexion, $query);
    }

    /**
     * Hace consulta multiple
     *
     * @param string $query
     *            cadena multi query
     */
    function ejecutarDeArchivo($query)
    {
        mysqli_multi_query($this->conexion, $query);
        while (mysqli_next_result($this->conexion))
        {
            ;
        }
    }

    /**
     * Regresa el error en mysql
     */
    function error()
    {
        return @mysqli_error($this->conexion);
    }

    /**
     * Regresa el ultimo id insertado
     */
    function idInsertado()
    {
        return @mysqli_insert_id($this->conexion);
    }

    /**
     * Regresa cada uno de los resultados como Objeto obj->field
     */
    function obtener_obj($resultado)
    {
        return @mysqli_fetch_object($resultado);
    }

    /**
     * Regresa cada uno de los resultados como Array obj[0]
     */
    function obtener_array($resultado)
    {
        return @mysqli_fetch_array($resultado);
    }

    /**
     * Regresa numero de filas en resultado
     */
    function total_filas($resultado)
    {
        return @mysqli_num_rows($resultado);
    }

    /**
     * Libera los recursos del resultado
     */
    function limpia_resultado($resultado)
    {
        return @mysqli_free_result($this->conexion, $resultado);
    }

    /**
     * Selecciona la base de datos a usar
     *
     * @param string $basedeDatos
     */
    function seleccionaBD($basedeDatos)
    {
        @mysqli_select_db($this->conexion, $basedeDatos);
    }

    /**
     * Crear la base de datos
     *
     * @param string $nombre
     */
    public function crearBD($nombre)
    {
        $query = "CREATE DATABASE IF NOT EXISTS $nombre DEFAULT CHARACTER SET utf8 COLLATE utf8_spanish_ci";
        return $this->ejecutar($query);
    }
}
