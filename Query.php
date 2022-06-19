<?php
/**
 * @version v2022_1
 * @author Martin Mata
 */
namespace Clases\MySql;

class Query extends Conexion
{

    /** @var Error **/
    public $error;
    
    /**
     * Inicia el objeto
     *
     * @param object $conexion
     */
    function __construct(string $base_datos = BD_GENERAL)
    {
        $this->error = new Error();       
        parent::__construct($base_datos);        
        if($this->conexion->select_db($base_datos))
        {
            if($resultado = $this->conexion->query("SHOW TABLES LIKE 'eliminados'"))
            {                                       
                if ($resultado->num_rows > 0) 
                    $this->conexion->query($this->tabla());                
            }
        }
    }   
    
    function __destruct()
    {
        if(is_resource($this->conexion))
            $this->conexion->close();
    }
    

    /**
     *
     * @param string $tabla
     *            <br><code>Nombre de la tabla a insertar</code>
     * @param \stdClass $datos
     *            <br><code>Clase con los datos a insertar</code>
     * @param string $base_datos
     *            <br><code>Base de datos a insertar datos</code>
     * @return int <br><code>numero de id insertado</code>
     */
    protected function insertar($tabla, $datos, $usuario = "guess")
    {            
        if ($this->conectado($this->base_datos))
        {
            $queryelements = "";
            $query = "INSERT INTO " . $this->conexion->real_escape_string($tabla) . " SET ";
            foreach ($datos as $key => $value)
            {
                $key = $this->conexion->real_escape_string($key);
                $value = $this->conexion->real_escape_string($value);
                $queryelements .= "$key = '$value',";
            }
            $queryelements = rtrim($queryelements, ',');
            $query .= $queryelements;
            try
            {
                $result = $this->conexion->query($query);
                if ($result !== FALSE)
                {
                    return $this->conexion->insert_id;                   
                }
                else
                {
                    $this->error->reporte(get_class($this) . __METHOD__, $query . "  " . $this->conexion->error);                    
                }
            }
            catch (\Exception $e)
            {
                $this->error->reporte(get_class($this) . __METHOD__, $query . "  " . $e->getMessage(), $usuario);
            }
        }
        else
        {
            return 0;
        }
        return 0;
    }

    /**
     *
     * @param string $tabla
     *            <br><code>Nombre de la tabla a insertar</code>
     * @param \stdClass $datos
     *            <br><code>Clase con los datos a insertar</code> 
     * @return int <br><code>Numero de id insertado</code>
     */
    protected function reemplazar($tabla, $datos, $usuario = "guess")
    {
        if ($this->conectado($this->base_datos))
        {
            $queryelements = "";
            $query = "REPLACE INTO " . $this->conexion->real_escape_string($tabla) . " SET ";
            foreach ($datos as $key => $value)
            {
                $key = $this->conexion->real_escape_string($key);
                $value = $this->conexion->real_escape_string($value);
                $queryelements .= "$key = '$value',";
            }
            $queryelements = rtrim($queryelements, ',');
            $query .= $queryelements;
            try
            {
                $result = $this->conexion->query($query);
                if ($result !== FALSE)
                {
                    return $this->conexion->insert_id;                  
                }
                else
                {
                    $this->error->reporte(get_class($this) . __METHOD__, $query . "  " . $this->conexion->error, $usuario);                    
                }
            }
            catch (\Exception $e)
            {
                $this->error->reporte(get_class($this) . __METHOD__, $query . "  " . $e->getMessage(), $usuario);
            }
        }
        else
        {
            return 0;
        }
        return 0;
    }

    /**
     *
     * @param string $tabla
     *            <br><code>Nombre de la Tabla a Editar</code>
     * @param \stdClass $datos
     *            <br><code>Clase con los datos a insertar</code>
     * @param string $id
     *            <br><code>Id a buscar</code>
     * @param string $buscar_por
     *            <br><code>Nombre del Campo que se busca </code>     
     * @return int <br><code>id a modificar true, 0 false</code>
     */
    protected function modificar($tabla, $datos, $id, $buscar_por,  $usuario = "guess")
    {
        if ($this->conectado($this->base_datos))
        {
            $queryelements = "";
            $query = "Update " . $tabla . " SET ";
            foreach ($datos as $key => $value)
            {
                $value = $value;
                $key = $this->conexion->real_escape_string($key);
                $value = $this->conexion->real_escape_string($value);
                $queryelements .= "$key = '$value',";
            }
            $queryelements = rtrim($queryelements, ',');
            $query .= $queryelements;
            $query .= " WHERE " . $buscar_por . " = '$id' ";
            //$this->error->reporte("aqui", $query);
            try
            {
                $result = $this->conexion->query($query);
                if ($result !== FALSE)
                {
                    return $id;
                }
                else
                {
                    $this->error->reporte(get_class($this) . __METHOD__, $query . "  " . $this->conexion->error, $usuario);
                }
            }
            catch (\Exception $e)
            {
                $this->error->reporte(get_class($this) . __METHOD__, $query . "  " . $e->getMessage(), $usuario);
            }
        }
        else
        {
            return 0;
        }
        return 0;
    }

    /**
     *
     * @param string $tabla
     *            <br><code>Nombre de la Tabla a Editar</code>
     * @param string $id
     *            <br><code>Id a buscar</code>
     * @param string $buscar_por
     *            <br><code>Nombre del Campo que se busca </code>     
     * @return int <br><code>1 true, 0 false</code>
     */
    protected function eliminar($tabla, $id, $busca_por, $usuario = "guess")
    {
        
        if ($this->conectado($this->base_datos))
        {
            try
            {
                $query = "DELETE FROM $tabla WHERE " . $busca_por . "='$id' ";
                $result = $this->conexion->query($query);
                if ($result !== FALSE)                
                {
                    $this->insertar("eliminados", array("EliTabla"=>$tabla, "EliCampoID"=>$id,"EliCampoNombre"=>$busca_por));
                    return 1;
                }
                else
                {
                    $this->error->reporte(get_class($this) . __METHOD__, $query . "  " . $this->conexion->error, $usuario);
                }
            }
            catch (\Exception $e)
            {
                $this->error->reporte(get_class($this) . __METHOD__, $query . "  " . $e->getMessage() . $usuario);
            }
        }
        else
        {
            return 0;
        }
        return 0;
    }
    
    /**
     *Usese con cuidado
     * @param string $tabla
     *            <br><code>Nombre de la Tabla a Editar</code>
     * @param string $condicion
     *            <br><code>condicion para eliminar</code>     
     * @return int <br><code>1 true, 0 false</code>
     */
    protected function eliminarEspecial($tabla, $condicion, $usuario = "guess")
    {
        if ($this->conectado($this->base_datos))
        {
            try
            {
                $query = "DELETE FROM $tabla WHERE $condicion";
                $result = $this->conexion->query($query);
                if ($result !== FALSE)
                {                    
                    return 1;
                }
                else
                {
                    $this->error->reporte(get_class($this) . __METHOD__, $query . "  " . $this->conexion->error, $usuario);
                }
            }
            catch (\Exception $e)
            {
                $this->error->reporte(get_class($this) . __METHOD__, $query . "  " . $e->getMessage() . $usuario);
            }
        }
        else
        {
            return 0;
        }
        return 0;
    }

    /**
     *
     * @param string $campos
     *            <br><code>Campo Nombre as nombre, campo id as id</code>
     * @param string $tabla
     *            <br><code>Nombre de la Tabla a Editar</code>    
     * @param string $sel_campo
     *            <br><code>Nombre del Campo a seleccionar</code>
     * @param string $seleccionado
     *            <br><code>Nombre o Valor del Campo Seleccionado </code>
     * @param string $where
     *            <br><code>campo = 'valor' and campo2 like "%valor2%" or campo3 = 'valor3' etc</code>
     * @param string $orderby
     *            <br><code> campo1, campo2, campo3 asc etc</code>
     * @param int $limit
     *            <br><code> 1</code>
     * @return string
     */
    protected function options($campos, $tabla, $sel_campo, $seleccionado = 0, $where = "0", $orderby = "0", $limit = 0)
    {        
        $options = "";
        if ($this->conectado($this->base_datos))
        {
            try
            {
                $query = "SELECT $campos FROM " . $tabla;
                if ($where != "0") $query .= " WHERE " . $where;
                if ($orderby != "0") $query .= " ORDER BY " . $orderby;
                if ($limit != 0) $query .= " Limit " . $limit;
                $result = $this->conexion->query($query);
                // $this->error->reporte("paises", $query , "admin");
                while ($fila = $result->fetch_object())
                {
                    $sltd = "";
                    if ($seleccionado == $fila->$sel_campo) $sltd = " selected ";

                    $options .= "<option value='" . $fila->id . "' " . $sltd . ">" . $fila->nombre . "</option>";
                }

                return (strlen($options) > 0) ? $options : 0;
            }
            catch (\Exception $e)
            {
                $this->error->reporte(get_class($this) . __METHOD__, $query . "  " . $e->getMessage(), "admin");
            }
        }
        else
            return 0;
        return 0;
    }

    /**
     *
     * @param string $campos
     *            <br><code>Nombre del campo o campos a consultar</code>
     * @param string $tabla
     *            <br><code>Nombre de la tabla o tablas, ya sea en inner join o union</code>
    
     * @param string $where
     *            <br><code>campo = 'valor' and campo2 like "%valor2%" or campo3 = 'valor3' etc</code>
     * @param string $orderby
     *            <br><code> campo1, campo2, campo3 asc etc</code>
     * @param string $groupby
     *            <br><code> campo1, campo2, campo3 </code>
     * @param string $limit
     *            <br><code> 1</code>
     */
    protected function consulta($campos, $tabla, $where = "0", $orderby = "0", $groupby = "0", $limit = "0", $usuario = "guess")
    {
        $datos = array();        
        if ($this->conectado($this->base_datos))
        {
            try
            {
                $query = "SELECT " . $campos . " FROM " .$tabla;
                if ($where != "0") $query .= " WHERE " . $where;
                if ($groupby != "0") $query .= " GROUP BY " .$groupby;
                if ($orderby != "0") $query .= " ORDER BY " . $orderby;
                if ($limit != "0") $query .= " Limit " . $limit;
                //$this->error->reporte("aqui", $query);
                $result = $this->conexion->query($query);
                while ($fila = $result->fetch_object())
                {
                    $datos[] = $fila;
                }
                return $datos;
            }
            catch (\Exception $e)
            {
                $this->error->reporte(get_class($this) . __METHOD__, $query . "  " . $e->getMessage(), $usuario);
            }
        }
        else
            return 0;
        return 0;
    }

    /**
     *
     * @param string $tabla
     *            <br><code>Nombre de la Tabla a Editar</code>
     * @param \stdClass $datos
     *            <br><code>Clase con los datos a insertar si los datos contienen cadenas, (string) use comillas para cubrielos "datos"</code>
     * @param string $where
     *            <br><code>multiples condiciones</code>     
     * @return int <br><code>id a modificar true, 0 false</code>
     */
    protected function modificarEspecial($tabla, $datos, $where, $usuario = "guess")
    {        
        if ($this->conectado($this->base_datos))
        {
            $queryelements = "";
            $query = "Update " . $tabla . " SET ";
            foreach ($datos as $key => $value)
            {                
                $queryelements .= "$key = $value,";
            }
            $queryelements = rtrim($queryelements, ',');
            $query .= $queryelements;
            if ($where != "0") $query .= " WHERE " . $where;
            // $this->error->reporte ( get_class ( $this ) . __METHOD__, $query . " ", $usuario );
            try
            {
                $result = $this->conexion->query($query);
                if ($result !== FALSE)
                {
                    return 1;
                }
                else
                {
                    $this->error->reporte(get_class($this) . __METHOD__, $query . "  " . $this->conexion->error, $usuario);                    
                }
            }
            catch (\Exception $e)
            {
                $this->error->reporte(get_class($this) . __METHOD__, $query . "  " . $e->getMessage(), $usuario);
               
            }
        }
        else
            return 0;
        return 0;
    }

    protected function ultiorecord($tabla, $id)
    {
        $datos = array();
        if ($this->conectado($this->base_datos))
        {
            try
            {
                $query = "SELECT MAX($id) as ultimo FROM $tabla";                
                //$this->error->reporte("aqui", $query);
                $result = $this->conexion->query($query);
                while ($fila = $result->fetch_object())
                {
                    $datos[] = $fila;
                }
                return $datos;
            }
            catch (\Exception $e)
            {
                $this->error->reporte(get_class($this) . __METHOD__, $query . "  " . $e->getMessage(), $usuario);
            }
        }
        else
            return 0;
        return 0;
    }
    /**
     *
     * @return string Estructura para base de datos mysql
     */
    private function tabla()
    {
        return "
            
			CREATE TABLE IF NOT EXISTS `eliminados` (
			  `EliID` int(11) NOT NULL AUTO_INCREMENT,
			  `EliTabla` varchar(40) NOT NULL,
			  `EliCampoID` int(11) NOT NULL ,
			  `EliCampoNombre` varchar(40) NOT NULL,
			  `updated` tinyint(1) NOT NULL,
			  PRIMARY KEY (`EliID`)
			) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";        
    }
}