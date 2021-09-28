<?php
/**
 * @version v2021_1
 * @author Martin Mata
 */
namespace clases\MySql;;

class Query
{

    /** @var Error **/
    public $error;

    /** @var Conexion **/
    public $conn;

    /**
     * Inicia el objeto
     *
     * @param object $conexion
     */
    function __construct($conexion = null)
    {
        $this->error = new Error();
        if ($conexion !== null)
        {
            $this->conn = $conexion;
        }
        else
        {
            $this->conn = new Conexion();
            $this->conn->conectar();
        }
        $this->conn->seleccionaBD(BD_GENERAL);
        if ($this->conn->estaConectado)
        {
            if ($resultado = $this->conn->ejecutar("SHOW TABLES LIKE 'eliminados'"))
            {
                if ($this->conn->total_filas($resultado) == 0) $this->conn->ejecutarDeArchivo($this->tabla());
            }
        }
    }

    /**
     * Cerrar conexion cuando la clase salga de scope
     */
    function __destruct()
    {
        //interrumpe transacciones por eso lo comentamos
        //$this->conn->cerrar();
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
    protected function insertar($tabla, $datos, $base_datos, $usuario = "guess")
    {
        $id_insertado = 0;
        $this->conn->seleccionaBD($base_datos);
        if ($this->conn->estaConectado)
        {
            $queryelements = "";
            $query = "INSERT INTO " . $this->conn->escape($tabla) . " SET ";
            foreach ($datos as $key => $value)
            {
                $key = $this->conn->escape($key);
                $value = $this->conn->escape($value);
                $queryelements .= "$key = '$value',";
            }
            $queryelements = rtrim($queryelements, ',');
            $query .= $queryelements;
            try
            {
                $result = $this->conn->ejecutar($query);
                if ($result !== FALSE)
                {
                    $id_insertado = $this->conn->idInsertado();

                    return $id_insertado;
                }
                else
                {
                    $this->error->reporte(get_class($this) . __METHOD__, $query . "  " . $this->conn->error(), $usuario);                    
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
     * @param string $base_datos
     *            <br><code>Base de datos a insertar datos</code>
     * @return int <br><code>Numero de id insertado</code>
     */
    protected function reemplazar($tabla, $datos, $base_datos, $usuario = "guess")
    {
        $id_insertado = 0;
        $this->conn->seleccionaBD($base_datos);
        if ($this->conn->estaConectado)
        {
            $queryelements = "";
            $query = "REPLACE INTO " . $this->conn->escape($tabla) . " SET ";
            foreach ($datos as $key => $value)
            {
                $key = $this->conn->escape($key);
                $value = $this->conn->escape($value);
                $queryelements .= "$key = '$value',";
            }
            $queryelements = rtrim($queryelements, ',');
            $query .= $queryelements;
            try
            {
                $result = $this->conn->ejecutar($query);
                if ($result !== FALSE)
                {
                    $id_insertado = $this->conn->idInsertado();

                    return $id_insertado;
                }
                else
                {
                    $this->error->reporte(get_class($this) . __METHOD__, $query . "  " . $this->conn->error(), $usuario);                    
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
     * @param string $base_datos
     *            <br><code>Base de datos a insertar datos</code>
     * @return int <br><code>id a modificar true, 0 false</code>
     */
    protected function modificar($tabla, $datos, $id, $buscar_por, $base_datos, $usuario = "guess")
    {
        $this->conn->seleccionaBD($base_datos);
        if ($this->conn->estaConectado)
        {
            $queryelements = "";
            $query = "Update " . $this->conn->escape($tabla) . " SET ";
            foreach ($datos as $key => $value)
            {
                $value = $value;
                $key = $this->conn->escape($key);
                $value = $this->conn->escape($value);
                $queryelements .= "$key = '$value',";
            }
            $queryelements = rtrim($queryelements, ',');
            $query .= $queryelements;
            $query .= " WHERE " . $this->conn->escape($buscar_por) . " = '$id' ";
            // $this->error->reporte("aqui", $query);
            try
            {
                $result = $this->conn->ejecutar($query);
                if ($result !== FALSE)
                {
                    return $id;
                }
                else
                {
                    $this->error->reporte(get_class($this) . __METHOD__, $query . "  " . $this->conn->error(), $usuario);
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
     * @param string $base_datos
     *            <br><code>Base de datos a insertar datos</code>
     * @return int <br><code>1 true, 0 false</code>
     */
    protected function eliminar($tabla, $id, $busca_por, $base_datos, $usuario = "guess")
    {
        $this->conn->seleccionaBD($base_datos);
        if ($this->conn->estaConectado)
        {
            try
            {
                $query = "DELETE FROM " . $this->conn->escape($tabla) . " WHERE " . $this->conn->escape($busca_por) . "='$id' ";
                $result = $this->conn->ejecutar($query);
                if ($result !== FALSE)                
                {
                    $this->insertar("eliminados", array("EliTabla"=>$tabla, "EliCampoID"=>$id,"EliCampoNombre"=>$busca_por), $base_datos);
                    return 1;
                }
                else
                {
                    $this->error->reporte(get_class($this) . __METHOD__, $query . "  " . $this->conn->error(), $usuario);
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
     * @param string $base_datos
     *            <br><code>Base de Datos</code>
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
    protected function options($campos, $tabla, $base_datos, $sel_campo, $seleccionado = 0, $where = "0", $orderby = "0", $limit = 0)
    {
        $this->conn->seleccionaBD($base_datos);
        $options = "";
        if ($this->conn->estaConectado)
        {
            try
            {
                $query = "SELECT $campos FROM " . $this->conn->escape($tabla);
                if ($where != "0") $query .= " WHERE " . $where;
                if ($orderby != "0") $query .= " ORDER BY " . $orderby;
                if ($limit != 0) $query .= " Limit " . $limit;
                $result = $this->conn->ejecutar($query);
                // $this->error->reporte("paises", $query , "admin");
                while ($fila = $this->conn->obtener_array($result))
                {
                    $sltd = "";
                    if ($seleccionado == $fila[$sel_campo]) $sltd = " selected ";

                    $options .= "<option value='" . $fila["id"] . "' " . $sltd . ">" . $fila["nombre"] . "</option>";
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
     * @param string $base_datos
     *            <br><code>Base de Datos</code>
     * @param string $where
     *            <br><code>campo = 'valor' and campo2 like "%valor2%" or campo3 = 'valor3' etc</code>
     * @param string $orderby
     *            <br><code> campo1, campo2, campo3 asc etc</code>
     * @param string $groupby
     *            <br><code> campo1, campo2, campo3 </code>
     * @param string $limit
     *            <br><code> 1</code>
     */
    protected function consulta($campos, $tabla, $base_datos, $where = "0", $orderby = "0", $groupby = "0", $limit = "0", $usuario = "guess")
    {
        $datos = array();
        $this->conn->seleccionaBD($base_datos);
        if ($this->conn->estaConectado)
        {
            try
            {
                $query = "SELECT " . $campos . " FROM " . $tabla;
                if ($where != "0") $query .= " WHERE " . $where;
                if ($groupby != "0") $query .= " GROUP BY " . $groupby;
                if ($orderby != "0") $query .= " ORDER BY " . $orderby;
                if ($limit != "0") $query .= " Limit " . $limit;
                // $this->error->reporte("aqui", $query);
                $result = $this->conn->ejecutar($query);
                while ($fila = $this->conn->obtener_obj($result))
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
     * @param string $base_datos
     *            <br><code>Base de datos a insertar datos</code>
     * @return int <br><code>id a modificar true, 0 false</code>
     */
    protected function modificarEspecial($tabla, $datos, $where, $base_datos, $usuario = "guess")
    {
        $this->conn->seleccionaBD($base_datos);
        if ($this->conn->estaConectado)
        {
            $queryelements = "";
            $query = "Update " . $this->conn->escape($tabla) . " SET ";
            foreach ($datos as $key => $value)
            {
                // $key = $this->conn->escape ( $key );
                // $value = $this->conn->escape ( $value );
                $queryelements .= "$key = $value,";
            }
            $queryelements = rtrim($queryelements, ',');
            $query .= $queryelements;
            if ($where != "0") $query .= " WHERE " . $where;
            // $this->error->reporte ( get_class ( $this ) . __METHOD__, $query . " ", $usuario );
            try
            {
                $result = $this->conn->ejecutar($query);
                if ($result !== FALSE)
                {
                    return 1;
                }
                else
                {
                    $this->error->reporte(get_class($this) . __METHOD__, $query . "  " . $this->conn->error(), $usuario);
                    
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

    /**
     *
     * @return string Estructura para base de datos mysql
     */
    private function tabla()
    {
        $mysql = "
            
			CREATE TABLE IF NOT EXISTS `eliminados` (
			  `EliID` int(11) NOT NULL AUTO_INCREMENT,
			  `EliTabla` varchar(40) NOT NULL,
			  `EliCampoID` int(11) NOT NULL ,
			  `EliCampoNombre` varchar(40) NOT NULL,
			  `updated` tinyint(1) NOT NULL,
			  PRIMARY KEY (`EliID`)
			) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
        return $mysql;
    }
}
