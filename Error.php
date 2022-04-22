<?php
/**
 * @version v2022_1
 * @author Martin Mata
 */
namespace Clases\MySql;

/**
 * ALmacena errores de mysql o cualquier otro error
 *
 * @author Martin
 *        
 */
class Error
{

    /**
     * Inicia el objeto y crea tabla error
     *
     * @param string $base_datos
     */
    function __construct($base_datos = BD_GENERAL)
    {
        $conn = new Conexion($base_datos);
        if($conn->conectado())
        {
            $conn->conexion->query("SHOW TABLES LIKE 'errores'");
            if($conn->conexion->field_count > 0)
            {                              
                $conn->conexion->query($this->tabla());
            }
        }
        $conn->conexion->close();
    }

    /**
     * Archiva errores en mysql table con nombre Errores
     *
     * @param string $donde
     * @param string $que
     * @param string $usuario
     */
    public function reporte($donde, $que, $usuario = "admin", $base_datos = BD_GENERAL)
    {
        @session_start();
        $conn = new Conexion($base_datos);
        if($conn->conectado())
        {        
            $query = "INSERT INTO errores VALUES(
			NULL,
			'" . $conn->conexion->real_escape_string($usuario) . "',
			'" . date("Y-m-d H:i:s") . "',
			'" . $conn->conexion->real_escape_string($donde) . "',
			'" . $conn->conexion->real_escape_string($que) . "')";
            $conn->conexion->query($query);
        } 
        else 
        {
            $this->error = $conn->conexion->error;
        }        
        return 0;
    }

    /**
     *
     * @return string Estructura para base de datos mysql
     */
    private function tabla()
    {
        $mysql = "
			
			CREATE TABLE IF NOT EXISTS `errores` (
			  `ErrID` int(11) NOT NULL AUTO_INCREMENT,
			  `ErrUsuario` varchar(20) NOT NULL,
			  `ErrHora` datetime NOT NULL,
			  `ErrDonde` varchar(100) NOT NULL,
			  `ErrQue` text NOT NULL,
			  PRIMARY KEY (`ErrID`)
			) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
        return $mysql;
    }
}
