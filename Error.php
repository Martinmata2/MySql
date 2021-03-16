<?php
/**
 * @version v2021_1
 * @author Martin Mata
 */
 //namespace puede ser editado a la direccion donde se encuentran los archivos
namespace MYSQL;

/**
 * Archiva errores para debug 
 *
 * @author Martin
 *        
 */
class Error
{

    /**
     * Inicia el objeto y crea tabla error
     * BD_GENERAL es una constante definida en autoload
     * @param string $base_datos
     */
    function __construct($base_datos = BD_GENERAL)
    {
        $conn = new Conexion();
        $conn->conectar();
        $conn->seleccionaBD($base_datos);
        if ($conn->estaConectado) 
        {
            if ($resultado = $conn->ejecutar("SHOW TABLES LIKE 'errores'")) 
            {
                if ($conn->total_filas($resultado) == 0)
                    $conn->ejecutarDeArchivo($this->tabla());
            }
        }
        $conn->cerrar();
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
        $conn = new Conexion();
        $conn->conectar();
        $conn->seleccionaBD($base_datos);
        if ($conn->estaConectado) 
        {
            $query = "INSERT INTO errores VALUES(
              NULL,
              '" . $conn->escape($usuario) . "',
              '" . date("Y-m-d H:i:s") . "',
              '" . $conn->escape($donde) . "',
              '" . $conn->escape($que) . "')";
            $conn->ejecutar($query);
        } 
        else 
        {
            $this->error = "No hay coneccion";
        }
        $conn->cerrar();
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
